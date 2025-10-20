# OAuth Flows for AI Identities

AI clients are non-interactive. Use OAuth 2.0 flows that do not require a browser or human consent screens.

## Supported Flows

### Client Credentials (RFC 6749)
- Best for service-to-service or headless agents.
- Issues access tokens bound to the client, optionally scoped to a specific AI identity.
- Token lifetime short; refresh via re-auth.

Example (token request):
```bash
curl -X POST https://your-host/oauth/token \
  -d 'grant_type=client_credentials' \
  -d 'client_id=CLIENT_ID' \
  -d 'client_secret=CLIENT_SECRET' \
  -d 'scope=context:read memory:write'
```

### JWT Bearer Token Grant (RFC 7523)
- The client signs a JWT with its private key and exchanges it for an access token.
- Stronger trust with key-based auth; enables mTLS or key rotation patterns.

Example (conceptual):
```bash
curl -X POST https://your-host/oauth/token \
  -d 'grant_type=urn:ietf:params:oauth:grant-type:jwt-bearer' \
  -d 'assertion=SIGNED_JWT' \
  -d 'scope=context:read memory:write'
```

## Token Types
- Access Token (JWT or opaque): short-lived; carries scopes.
- Refresh Token (optional): for longer-lived sessions; limit usage for AI.
- Introspection endpoint validates tokens; revocation supported.

## Scopes
- See [Scopes & Policies](./scopes-policies.md). Request only what is needed.

## Recommendations
- Prefer Client Credentials for simplicity and reliability.
- Consider JWT bearer for stronger key management and auditing.
- Keep access tokens short-lived (5–30 minutes). Re-issue as needed.
- Use mTLS or IP allowlists for high-sensitivity workloads.