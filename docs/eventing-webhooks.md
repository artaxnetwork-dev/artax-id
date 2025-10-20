# Eventing & Webhooks

Use events and webhooks to keep orchestrators synchronized with context and memory changes.

## Event Types
- `memory.adjustment.created`
- `memory.adjustment.approved`
- `memory.adjustment.rejected`
- `context.updated`
- `identity.updated`

## Webhook Delivery
- Configure endpoints in the admin dashboard.
- Retries with exponential backoff on failure.
- Idempotency with `event_id` and signature validation.

## Security
- HMAC signature header (e.g., `X-Artax-Signature` with SHA-256).
- Include timestamp and `event_id` to prevent replay.
- Optional mTLS for high-security integrations.

## Payload Example
```json
{
  "event_id": "evt_123",
  "type": "memory.adjustment.approved",
  "identity_id": "agent-123",
  "created_at": "2025-01-01T12:00:00Z",
  "data": {
    "adjustment": {
      "id": "adj_456",
      "subject": "report_frequency",
      "operation": "set",
      "value": "weekly",
      "confidence": 0.92,
      "rationale": "user preference detected"
    }
  }
}
```

## Failure Handling
- Dead-letter queue for undeliverable events.
- Admin dashboard shows endpoint health and last delivery status.
- Manual replay supported per `event_id`.

## Ordering
- Best-effort ordering per identity.
- Consumers should be idempotent and able to tolerate out-of-order delivery.