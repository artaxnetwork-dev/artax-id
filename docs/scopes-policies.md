# Scopes & Policies

Scopes grant granular permissions; policies add contextual rules.

## Scopes
- `context:read`: read identity context.
- `context:write`: update identity context.
- `memory:read`: query memory records.
- `memory:write`: propose memory adjustments.
- `events:read`: read logged events.
- `events:write`: write agent events.
- `identity:read`: read identity metadata.
- `identity:write`: update identity metadata (rare; admin-reviewed).
- `webhook:manage`: manage webhook endpoints (admin-only via dashboard).
- `audit:read`: read audit logs (restricted).

## Policy Examples
- Auto-approve `memory:write` when `confidence >= 0.9` and non-sensitive subjects.
- Require manual approval for PII-related subjects or low confidence.
- Limit `context:write` to specific JSON paths.
- Rate limits per scope, e.g., `memory:write` 60/min per identity.
- Time-bound scopes (token must carry `nbf`/`exp` windows).
- Tenant isolation: deny cross-tenant access regardless of scopes.

## Enforcement Order
1. Authenticate token.
2. Validate scopes.
3. Apply policies (path, tags, rate limits, sensitivity).
4. Record audit and produce events.

## Approvals Workflow
- `pending` -> `approved` or `rejected` by policy/administrator.
- Admin UI shows rationale and diffs.
- Webhooks emit status changes to orchestrators.