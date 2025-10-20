# API Reference (v1)

Base URL: `https://your-host/api/v1`
Authentication: Bearer token (OAuth 2.0). Required scopes documented per endpoint.

## Context

### GET /ai/{identity_id}/context
- Scope: `context:read`
- Returns current working context for the AI identity.
- Query: `since` (optional ISO8601) to fetch changes since a timestamp.

Example response:
```json
{
  "identity_id": "agent-123",
  "revision": 42,
  "data": {
    "goals": ["summarize daily reports"],
    "constraints": ["no PII output"],
    "env": {"timezone": "UTC"}
  },
  "updated_at": "2025-01-01T12:00:00Z"
}
```

### PATCH /ai/{identity_id}/context
- Scope: `context:write`
- Applies targeted updates to context.
- Body (example):
```json
{
  "ops": [
    {"op": "replace", "path": "/env/timezone", "value": "Europe/London"},
    {"op": "add", "path": "/goals/-", "value": "compile weekly summary"}
  ],
  "reason": "shifted reporting window"
}
```

## Memory

### GET /ai/{identity_id}/memory
- Scope: `memory:read`
- Filters: `q` (search), `tag`, `limit`, `cursor`.

### POST /ai/{identity_id}/memory/adjustments
- Scope: `memory:write`
- Proposes changes to durable memory.
- Body (example):
```json
{
  "adjustments": [
    {
      "subject": "report_frequency",
      "operation": "set",
      "value": "weekly",
      "confidence": 0.92,
      "tags": ["ops"],
      "expires_at": null,
      "rationale": "user preference detected"
    }
  ]
}
```
- Response includes status: `pending`, `approved`, or `rejected` (policy-dependent).

## Events

### POST /ai/{identity_id}/events
- Scope: `events:write`
- Logs agent actions or notable occurrences.

### GET /ai/{identity_id}/events
- Scope: `events:read`
- Query by time window, type, tag.

## OAuth Helpers

### POST /oauth/token
- See [OAuth Flows](./oauth-flows.md).

### POST /oauth/revoke
- Revokes a token.

### POST /oauth/introspect
- Validates token and returns scopes and expiry.

## Errors
- 401 Unauthorized: missing/invalid token.
- 403 Forbidden: insufficient scope or policy restriction.
- 409 Conflict: revision mismatch on context update.
- 422 Unprocessable Entity: validation errors.

## Idempotency
- Use `Idempotency-Key` header for `POST` requests to ensure safe retries.

## Rate Limits
- Enforced per identity/tenant and scope. Exceeding returns `429 Too Many Requests`.