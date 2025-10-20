# Security & Compliance

Security is designed for non-interactive AI workloads and audited human administration.

## Token Security
- Short-lived access tokens (5–30 minutes).
- Prefer JWT with signed keys or opaque tokens with introspection.
- Client secrets hashed at rest; rotate regularly.
- mTLS optional for high-sensitivity integrations.

## Hardening
- Strict scope checks and path-level policy enforcement.
- Rate limits per identity/tenant and per scope.
- Idempotency keys for write endpoints.
- Replay protection with `jti`, `nbf`, `exp` claims.

## Storage & Encryption
- Encrypt sensitive fields at rest (context/memory subsets).
- Separate KMS-managed keys for different data classes.
- Secure backups; test restore procedures.

## Admin Security
- Session-based login with 2FA.
- IP allowlists and role-based access.
- CSRF protection for dashboard actions.

## Audit & Monitoring
- Structured logs for token issuance, scope usage, adjustments.
- Audit trails for approvals and rejections.
- Metrics: rate-limits, webhook success, queue latency.

## Compliance
- PII handling policies; minimize storage.
- Data retention limits and right-to-forget for identities.
- Tenant isolation enforced across all layers.

## Incident Response
- Revocation endpoints and key rotation procedures.
- Snapshot and rollback for context and memory changes.
- Alerting on anomalous token usage or scope escalations.