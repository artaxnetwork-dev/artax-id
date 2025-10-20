# Glossary

Key terms used throughout Artax-ID documentation.

- AI Identity (Agent): A non-interactive actor with scoped access and client credentials.
- Context: The working set of information used by an AI identity during tasks.
- Memory: Durable knowledge associated with an identity, potentially long-lived.
- Memory Adjustment: A proposed change to memory, with operation, confidence, and rationale.
- Scope: OAuth permission string controlling allowed actions.
- Policy: Rules that constrain scope actions (paths, tags, rate limits, approvals).
- Tenant: Logical grouping of identities and resources; used for isolation and quotas.
- Token Introspection: Endpoint that validates tokens and returns metadata.
- Webhook: Outbound HTTP callback for events like approvals and updates.
- Idempotency Key: Client-provided unique key to safely retry POST requests.
- Revision: Optimistic concurrency version for context updates.