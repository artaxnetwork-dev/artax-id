# Admin Access (Laravel Login)

Human administrators authenticate via the standard Laravel login page (`/login`). Admin actions never use OAuth.

## Roles
- Admin: full control over identities, scopes, policies, approvals, and audits.
- Observer: read-only access to dashboards, logs, and status.

## Capabilities
- Provision AI identities and client credentials.
- Configure scopes, rate limits, and policies per identity/tenant.
- Review and approve memory adjustments (manual or rules-based auto-approve).
- Manage webhooks, keys, rotations, and revocations.
- Inspect audit logs and token introspection details.

## Session Security
- Enforce 2FA, strong password policy, and session timeouts.
- IP allowlist optional for admin routes.
- CSRF and same-site cookie settings enabled by default.

## Dashboard Views
- Identities: list, create, rotate secrets, deactivate.
- Context & Memory: view current state, diffs, and history.
- Adjustments: queue, status (pending/approved/rejected), and rationale.
- Webhooks: endpoints, health, retries.
- Audit: token usage, scope access, errors.

## Operational Tasks
- Key rotation and secret management.
- Policy tuning based on usage and error rates.
- Reviewing high-risk adjustments and sensitive data access.