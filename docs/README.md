# Artax-ID: AI-Focused OAuth Server Documentation

Artax-ID is an OAuth 2.0 server dedicated to AI usage. Human administrators access the dashboard through the standard Laravel session login (`/login`). AI clients authenticate via OAuth to retrieve identity-specific context and propose memory knowledge adjustments.

This documentation set explains the architecture, flows, APIs, data models, security, and operations for building and integrating AI systems with Artax-ID.

## Principles
- Dedicated to AI workloads: non-interactive, secure, predictable.
- Human admins use Laravel login, never OAuth, for dashboard access.
- Identity-scoped context and memory with explicit policies and audit.
- Minimal friction for orchestration systems like Artax-Eye.

## What You Can Do
- Issue and validate tokens for AI identities.
- Read/write context and memory for a specific AI identity.
- Propose, review, and approve memory adjustments.
- Integrate with orchestrators via APIs and webhooks.

## Table of Contents
- [Architecture](./architecture.md)
- [OAuth Flows](./oauth-flows.md)
- [Admin Access](./admin.md)
- [API Reference](./api.md)
- [Data Models](./data-models.md)
- [Scopes & Policies](./scopes-policies.md)
- [Security](./security.md)
- [Integration Guide](./integration.md)
- [Eventing & Webhooks](./eventing-webhooks.md)
- [Runbook](./runbook.md)
- [Glossary](./glossary.md)

## Scope of This Server
- Primary audience: AI agents, orchestrators, pipelines.
- Human administrators: manage identities, policies, audits, approvals.
- Not intended for end-user login flows; AI tokens are non-interactive.

## Quick Start
1. Review [OAuth Flows](./oauth-flows.md) to pick the correct non-interactive flow.
2. Provision an AI identity and client credentials via the dashboard.
3. Request a token and call the [API Reference](./api.md) endpoints with appropriate scopes.
4. Use [Eventing & Webhooks](./eventing-webhooks.md) to stay in sync with memory changes.

## Terminology
- AI Identity: A logical actor (agent) with scoped access.
- Context: The working set of information the AI uses right now.
- Memory: Durable, longer-lived knowledge associated with an identity.
- Memory Adjustment: Proposed change to stored memory with metadata.

## Related Projects
- Artax-Eye (orchestrator): integrates to pull context and push adjustments.

For implementation details and examples, see the files linked above.