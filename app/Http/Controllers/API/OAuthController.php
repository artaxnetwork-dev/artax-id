<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\OAuthClient;
use App\Models\OAuthToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class OAuthController extends Controller
{
    public function issue(Request $request)
    {
        $validated = $request->validate([
            'grant_type' => 'required|string|in:client_credentials,jwt_bearer',
            'client_id' => 'nullable|string', // required for client_credentials
            'client_secret' => 'nullable|string', // required for client_credentials
            'scope' => 'nullable|string',
            'assertion' => 'nullable|string', // required for jwt_bearer
        ]);

        if ($validated['grant_type'] === 'client_credentials') {
            if (empty($validated['client_id']) || empty($validated['client_secret'])) {
                return response()->json([
                    'error' => 'invalid_client',
                    'error_description' => 'Client authentication failed',
                ], 401);
            }

            Log::info('OAuth issue: request validated', ['client_id' => $validated['client_id']]);
            $client = OAuthClient::where('client_id', $validated['client_id'])->first();
            if (!$client) {
                Log::warning('OAuth issue: client not found', ['client_id' => $validated['client_id']]);
                return response()->json([
                    'error' => 'invalid_client',
                    'error_description' => 'Client authentication failed',
                ], 401);
            }
            if (!Hash::check($validated['client_secret'], $client->client_secret_hash)) {
                Log::warning('OAuth issue: invalid secret', ['client_id' => $validated['client_id']]);
                return response()->json([
                    'error' => 'invalid_client',
                    'error_description' => 'Client authentication failed',
                ], 401);
            }

            $scopes = $validated['scope'] ?? '';
            // Optional: validate scopes against allowed_scopes
            if (!empty($client->allowed_scopes)) {
                $requested = array_filter(explode(' ', $scopes));
                $allowed = $client->allowed_scopes ?? [];
                foreach ($requested as $s) {
                    if (!in_array($s, $allowed, true)) {
                        return response()->json([
                            'error' => 'invalid_scope',
                            'error_description' => "Requested scope '{$s}' is not allowed",
                        ], 400);
                    }
                }
            }

            $now = Carbon::now();
            $expires = $now->copy()->addHour();

            $jwt = $this->makeJwt([
                'iss' => config('app.url') ?? config('app.name'),
                'sub' => $client->client_id,
                'aud' => 'artax-id',
                'iat' => $now->timestamp,
                'exp' => $expires->timestamp,
                'scope' => $scopes,
            ]);

            OAuthToken::create([
                'id' => (string) Str::uuid(),
                'client_id' => $client->client_id,
                'subject_id' => null,
                'scopes' => $scopes,
                'jwt' => $jwt,
                'expires_at' => $expires,
                'revoked' => false,
            ]);

            return response()->json([
                'access_token' => $jwt,
                'token_type' => 'Bearer',
                'expires_in' => $expires->diffInSeconds($now),
                'scope' => $scopes,
            ]);
        }

        // JWT Bearer Grant (RFC 7523-inspired minimal implementation)
        if ($validated['grant_type'] === 'jwt_bearer') {
            if (empty($validated['assertion'])) {
                return response()->json([
                    'error' => 'invalid_request',
                    'error_description' => 'assertion is required for jwt_bearer grant',
                ], 400);
            }

            $claims = $this->verifyJwt($validated['assertion']);
            if (!$claims) {
                return response()->json([
                    'error' => 'invalid_grant',
                    'error_description' => 'invalid JWT assertion',
                ], 400);
            }
            $clientIdFromAssertion = $claims['iss'] ?? $claims['sub'] ?? null;
            if (!$clientIdFromAssertion) {
                return response()->json([
                    'error' => 'invalid_grant',
                    'error_description' => 'assertion missing iss/sub',
                ], 400);
            }

            $client = OAuthClient::where('client_id', $clientIdFromAssertion)->first();
            if (!$client) {
                return response()->json([
                    'error' => 'invalid_client',
                    'error_description' => 'Client not found',
                ], 401);
            }

            $scopes = $validated['scope'] ?? ($claims['scope'] ?? '');
            if (!empty($client->allowed_scopes)) {
                $requested = array_filter(explode(' ', $scopes));
                $allowed = $client->allowed_scopes ?? [];
                foreach ($requested as $s) {
                    if (!in_array($s, $allowed, true)) {
                        return response()->json([
                            'error' => 'invalid_scope',
                            'error_description' => "Requested scope '{$s}' is not allowed",
                        ], 400);
                    }
                }
            }

            $now = Carbon::now();
            $expires = $now->copy()->addHour();

            $jwt = $this->makeJwt([
                'iss' => config('app.url') ?? config('app.name'),
                'sub' => $client->client_id,
                'aud' => 'artax-id',
                'iat' => $now->timestamp,
                'exp' => $expires->timestamp,
                'scope' => $scopes,
            ]);

            OAuthToken::create([
                'id' => (string) Str::uuid(),
                'client_id' => $client->client_id,
                'subject_id' => null,
                'scopes' => $scopes,
                'jwt' => $jwt,
                'expires_at' => $expires,
                'revoked' => false,
            ]);

            return response()->json([
                'access_token' => $jwt,
                'token_type' => 'Bearer',
                'expires_in' => $expires->diffInSeconds($now),
                'scope' => $scopes,
            ]);
        }
    }

    public function introspect(Request $request)
    {
        $validated = $request->validate([
            'token' => 'required|string',
        ]);

        $token = OAuthToken::where('jwt', $validated['token'])->first();
        $valid = false;
        $claims = null;

        if ($token && !$token->revoked) {
            $claims = $this->verifyJwt($token->jwt);
            if ($claims) {
                $now = Carbon::now()->timestamp;
                $valid = ($claims['exp'] ?? 0) > $now;
            }
        }

        return response()->json([
            'active' => $valid,
            'scope' => $token?->scopes,
            'client_id' => $token?->client_id,
            'sub' => $claims['sub'] ?? null,
            'exp' => $claims['exp'] ?? null,
            'iat' => $claims['iat'] ?? null,
            'iss' => $claims['iss'] ?? null,
            'aud' => $claims['aud'] ?? null,
        ]);
    }

    private function getSigningKey(): string
    {
        $key = config('app.key');
        if (str_starts_with($key, 'base64:')) {
            return base64_decode(substr($key, 7)) ?: 'artax-secret';
        }
        return $key ?: 'artax-secret';
    }

    private function makeJwt(array $claims): string
    {
        $header = ['alg' => 'HS256', 'typ' => 'JWT'];
        $segments = [
            rtrim(strtr(base64_encode(json_encode($header)), '+/', '-_'), '='),
            rtrim(strtr(base64_encode(json_encode($claims)), '+/', '-_'), '='),
        ];
        $signingInput = implode('.', $segments);
        $signature = hash_hmac('sha256', $signingInput, $this->getSigningKey(), true);
        $segments[] = rtrim(strtr(base64_encode($signature), '+/', '-_'), '=');
        return implode('.', $segments);
    }

    private function verifyJwt(string $jwt): ?array
    {
        $parts = explode('.', $jwt);
        if (count($parts) !== 3) {
            return null;
        }
        [$header64, $payload64, $sig64] = $parts;
        $signingInput = $header64 . '.' . $payload64;
        $expected = rtrim(strtr(base64_encode(hash_hmac('sha256', $signingInput, $this->getSigningKey(), true)), '+/', '-_'), '=');
        if (!hash_equals($expected, $sig64)) {
            return null;
        }
        $payload = json_decode(base64_decode(strtr($payload64, '-_', '+/')), true);
        return is_array($payload) ? $payload : null;
    }

    public function revoke(Request $request)
    {
        $validated = $request->validate([
            'token' => 'required|string',
        ]);

        $token = OAuthToken::where('jwt', $validated['token'])->first();
        if ($token) {
            $token->revoked = true;
            $token->save();
            return response()->json(['revoked' => true]);
        }

        // Per RFC 7009, the endpoint does not disclose invalid tokens
        return response()->json(['revoked' => false]);
    }
}