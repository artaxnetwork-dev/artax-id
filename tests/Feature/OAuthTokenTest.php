<?php

use App\Models\OAuthClient;
use Illuminate\Support\Carbon;

function testSigningKey(): string {
    $key = config('app.key');
    if (str_starts_with($key, 'base64:')) {
        return base64_decode(substr($key, 7)) ?: 'artax-secret';
    }
    return $key ?: 'artax-secret';
}

function b64url($data): string {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function makeTestJwt(array $claims): string {
    $header = ['alg' => 'HS256', 'typ' => 'JWT'];
    $segments = [
        b64url(json_encode($header)),
        b64url(json_encode($claims)),
    ];
    $sig = hash_hmac('sha256', implode('.', $segments), testSigningKey(), true);
    $segments[] = b64url($sig);
    return implode('.', $segments);
}

beforeEach(function () {
    $this->withoutExceptionHandling();
});

it('issues a client_credentials token and introspects as active', function () {
    $client = OAuthClient::create([
        'name' => 'Test Client',
        'client_id' => 'client-123',
        'client_secret_hash' => password_hash('secret-xyz', PASSWORD_BCRYPT),
        'allowed_scopes' => ['ai:context:read', 'ai:context:write'],
    ]);

    $issue = $this->postJson('/api/oauth/token', [
        'grant_type' => 'client_credentials',
        'client_id' => 'client-123',
        'client_secret' => 'secret-xyz',
        'scope' => 'ai:context:read ai:context:write',
    ])->assertOk()->json();

    expect($issue['token_type'])->toBe('Bearer');
    expect($issue['access_token'])->toBeString();

    $introspect = $this->postJson('/api/oauth/introspect', [
        'token' => $issue['access_token'],
    ])->assertOk()->json();

    expect($introspect['active'])->toBeTrue();
    expect($introspect['client_id'])->toBe('client-123');
});

it('rejects invalid client credentials', function () {
    $this->postJson('/api/oauth/token', [
        'grant_type' => 'client_credentials',
        'client_id' => 'nope',
        'client_secret' => 'nope',
    ])->assertStatus(401)->assertJson([
        'error' => 'invalid_client',
    ]);
});

it('rejects unauthorized scope', function () {
    $client = OAuthClient::create([
        'name' => 'Limited Client',
        'client_id' => 'limited-1',
        'client_secret_hash' => password_hash('s3cret', PASSWORD_BCRYPT),
        'allowed_scopes' => ['ai:context:read'],
    ]);

    $this->postJson('/api/oauth/token', [
        'grant_type' => 'client_credentials',
        'client_id' => 'limited-1',
        'client_secret' => 's3cret',
        'scope' => 'ai:context:write',
    ])->assertStatus(400)->assertJson([
        'error' => 'invalid_scope',
    ]);
});

it('issues a jwt_bearer token and introspects as active', function () {
    $client = OAuthClient::create([
        'name' => 'JWT Client',
        'client_id' => 'client-jwt-1',
        'client_secret_hash' => password_hash('unused-secret', PASSWORD_BCRYPT),
        'allowed_scopes' => ['ai:context:read'],
    ]);

    $now = Carbon::now();
    $assertion = makeTestJwt([
        'iss' => 'client-jwt-1',
        'sub' => 'client-jwt-1',
        'aud' => 'artax-id',
        'iat' => $now->timestamp,
        'exp' => $now->copy()->addHour()->timestamp,
        'scope' => 'ai:context:read',
    ]);

    $issue = $this->postJson('/api/oauth/token', [
        'grant_type' => 'jwt_bearer',
        'assertion' => $assertion,
    ])->assertOk()->json();

    expect($issue['token_type'])->toBe('Bearer');
    expect($issue['access_token'])->toBeString();

    $introspect = $this->postJson('/api/oauth/introspect', [
        'token' => $issue['access_token'],
    ])->assertOk()->json();

    expect($introspect['active'])->toBeTrue();
    expect($introspect['client_id'])->toBe('client-jwt-1');
});

it('revokes a token and makes it inactive on introspection', function () {
    $client = OAuthClient::create([
        'name' => 'Revoker Client',
        'client_id' => 'client-revoke-1',
        'client_secret_hash' => password_hash('revoker', PASSWORD_BCRYPT),
        'allowed_scopes' => ['ai:context:read'],
    ]);

    $issue = $this->postJson('/api/oauth/token', [
        'grant_type' => 'client_credentials',
        'client_id' => 'client-revoke-1',
        'client_secret' => 'revoker',
        'scope' => 'ai:context:read',
    ])->assertOk()->json();

    $this->postJson('/api/oauth/revoke', [
        'token' => $issue['access_token'],
    ])->assertOk()->assertJson(['revoked' => true]);

    $introspect = $this->postJson('/api/oauth/introspect', [
        'token' => $issue['access_token'],
    ])->assertOk()->json();

    expect($introspect['active'])->toBeFalse();
});