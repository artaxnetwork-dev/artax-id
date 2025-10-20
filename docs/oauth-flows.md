# OAuth 2.0 Flows for AI Clients

This document describes the OAuth 2.0 flows implemented for non-interactive AI clients.

## Endpoints

- POST /api/oauth/token
  - Supports grant_type: client_credentials, jwt_bearer
- POST /api/oauth/introspect
  - Returns token activity and claims
- POST /api/oauth/revoke
  - Revokes tokens (RFC 7009 semantics)

All endpoints are rate limited via the `api` limiter.

## Client Credentials Grant

Request:

POST /api/oauth/token
{
  "grant_type": "client_credentials",
  "client_id": "<client-id>",
  "client_secret": "<client-secret>",
  "scope": "ai:context:read ai:context:write"
}

Response:
{
  "access_token": "<jwt>",
  "token_type": "Bearer",
  "expires_in": 3600,
  "scope": "ai:context:read ai:context:write"
}

- Scopes are enforced against OAuthClient.allowed_scopes
- Token TTL defaults to 1 hour

## JWT Bearer Grant (Assertion)

Request:

POST /api/oauth/token
{
  "grant_type": "jwt_bearer",
  "assertion": "<signed-jwt>",
  "scope": "ai:context:read"
}

- The assertion must be an HS256-signed JWT using the server signing key derived from APP_KEY.
- The `iss` or `sub` claim must match an existing OAuth client id.
- Optional `scope` may also be provided inside the assertion payload.

Response (same as client credentials):
{
  "access_token": "<jwt>",
  "token_type": "Bearer",
  "expires_in": 3600,
  "scope": "ai:context:read"
}

## Token Introspection

Request:

POST /api/oauth/introspect
{
  "token": "<jwt>"
}

Response:
{
  "active": true|false,
  "scope": "...",
  "client_id": "...",
  "sub": "...",
  "exp": 1730000000,
  "iat": 1729996400,
  "iss": "...",
  "aud": "artax-id"
}

- `active` is true only if the token signature matches, the token has not been revoked, and `exp` > now.

## Token Revocation

Request:

POST /api/oauth/revoke
{
  "token": "<jwt>"
}

Response:
{
  "revoked": true
}

- If the token is unknown, the endpoint returns `{ "revoked": false }` to avoid disclosing unknown tokens.
- Once revoked, introspection will return `active: false`.

## Error Codes

- invalid_client (401): client authentication failed
- invalid_scope (400): requested scope is not allowed for the client
- invalid_request (400): missing required fields
- invalid_grant (400): invalid assertion for jwt_bearer

## Signing Key

Tokens are signed using HS256 with a key derived from `config('app.key')`.
- If APP_KEY starts with `base64:`, the decoded value is used.
- Otherwise the plain APP_KEY value is used.

## Notes

- The implementation keeps tokens in `oauth_tokens` with `revoked` and `expires_at` tracked.
- Adjust the API rate limiter settings in `AppServiceProvider` as needed for production.