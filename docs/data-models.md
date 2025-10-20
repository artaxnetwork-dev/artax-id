# Data Models

Core entities supporting AI identity, context, memory, and audit.

## AIIdentity
- `id`: string (stable identifier)
- `name`: string
- `tenant_id`: string|null
- `status`: active|inactive
- `default_scopes`: array
- `tags`: array
- `created_at`, `updated_at`

## ContextMemory
- `id`: string
- `identity_id`: string
- `revision`: integer (optimistic concurrency)
- `data`: JSON (structured context)
- `updated_at`

## MemoryRecord
- `id`: string
- `identity_id`: string
- `subject`: string
- `value`: JSON|string
- `tags`: array
- `created_at`, `expires_at`|null

## MemoryAdjustment
- `id`: string
- `identity_id`: string
- `operation`: set|unset|append|remove|merge
- `subject`: string
- `value`: JSON|string|null
- `confidence`: float [0..1]
- `status`: pending|approved|rejected
- `rationale`: string
- `created_by`: client_id
- `approved_by`: user_id|null
- `created_at`, `updated_at`

## OAuth
- `Client`: id, secret (hashed), allowed_scopes, metadata.
- `AccessToken`: id, client_id, scopes, expires_at, identity_id(optional), revoked.
- `RefreshToken`: id, access_token_id, expires_at, revoked.

## AuditLog
- `id`: string
- `actor_type`: admin|ai
- `actor_id`: user_id|client_id
- `action`: create|update|delete|approve|reject
- `resource_type`: identity|context|memory|adjustment|webhook|token
- `resource_id`: string
- `metadata`: JSON
- `created_at`