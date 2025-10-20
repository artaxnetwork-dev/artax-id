# Architecture

Artax-ID is a Laravel-based OAuth 2.0 server designed for AI workloads. Human admins access the dashboard via standard Laravel session login. AI clients use OAuth to read/write identity-scoped context and memory.

## Components
- Laravel App: HTTP/API server, dashboard, policy, audit.
- OAuth Layer: RFC-compliant token issuance, introspection, revocation (e.g., Passport/League OAuth2).
- Data Store: AI identities, context, memory, adjustments, audit logs.
- Queue/Jobs: async processing of adjustments, webhooks, approvals.
- Webhooks: outbound notifications to orchestrators (e.g., Artax-Eye).

## Identity Model
- Human Admin: session-authenticated, role-based (admin/observer).
- AI Identity (Agent): non-interactive actor with client credentials and scoped access.

## High-Level Flow
1. Admin provisions an AI identity and sets allowed scopes/policies.
2. AI requests a token via a non-interactive OAuth flow.
3. AI calls APIs to fetch context or propose memory adjustments.
4. Policies enforce rate limits, scope checks, and approval rules.
5. Events and webhooks notify orchestrators of changes.

## Data Boundaries
- Each AI identity has isolated context/memory.
- Cross-identity access requires explicit policy and scope.
- Sensitive fields encrypted at rest; access audited.

## Deployment Considerations
- Stateless HTTP with sticky sessions only for admin UI.
- Token secrets managed via env/secret manager; rotated regularly.
- Database backups and migration discipline.
- Observability: structured logs, metrics, tracing for adjustments and webhooks.

## Multi-Tenancy
- Tenant ID optional; attach to AI identities and resources.
- Policies enforced per tenant; quotas and rate limits per tenant.

## Extensibility
- Plugin-style policies for custom approval logic.
- Additional stores (vector DB) for memory, via adapters.
- Custom scopes for feature-specific APIs.