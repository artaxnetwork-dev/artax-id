# Integration Guide (Artax-Eye & Others)

This guide shows how orchestrators and AI agents integrate with Artax-ID.

## Steps
1. Admin provisions an AI identity and client credentials.
2. Orchestrator requests a token (Client Credentials or JWT Bearer).
3. Agent fetches context and submits memory adjustments.
4. Subscribe to webhooks for approved adjustments and context changes.

## Token (Client Credentials)
```bash
curl -X POST https://your-host/oauth/token \
  -d 'grant_type=client_credentials' \
  -d 'client_id=CLIENT_ID' \
  -d 'client_secret=CLIENT_SECRET' \
  -d 'scope=context:read memory:write'
```

## TypeScript Example
```ts
const token = await fetch("https://your-host/oauth/token", {
  method: "POST",
  headers: {"Content-Type": "application/x-www-form-urlencoded"},
  body: new URLSearchParams({
    grant_type: "client_credentials",
    client_id: process.env.CLIENT_ID!,
    client_secret: process.env.CLIENT_SECRET!,
    scope: "context:read memory:write",
  }),
}).then(r => r.json());

const ctx = await fetch("https://your-host/api/v1/ai/agent-123/context", {
  headers: {Authorization: `Bearer ${token.access_token}`},
}).then(r => r.json());

await fetch("https://your-host/api/v1/ai/agent-123/memory/adjustments", {
  method: "POST",
  headers: {
    Authorization: `Bearer ${token.access_token}`,
    "Content-Type": "application/json",
    "Idempotency-Key": crypto.randomUUID(),
  },
  body: JSON.stringify({
    adjustments: [{
      subject: "report_frequency",
      operation: "set",
      value: "weekly",
      confidence: 0.92,
      tags: ["ops"],
      rationale: "user preference detected",
    }],
  }),
});
```

## PHP Example
```php
$token = Http::asForm()->post('https://your-host/oauth/token', [
  'grant_type' => 'client_credentials',
  'client_id' => env('CLIENT_ID'),
  'client_secret' => env('CLIENT_SECRET'),
  'scope' => 'context:read memory:write',
])->json();

$ctx = Http::withToken($token['access_token'])
  ->get('https://your-host/api/v1/ai/agent-123/context')
  ->json();

Http::withToken($token['access_token'])
  ->withHeaders(['Idempotency-Key' => Str::uuid()])
  ->post('https://your-host/api/v1/ai/agent-123/memory/adjustments', [
    'adjustments' => [[
      'subject' => 'report_frequency',
      'operation' => 'set',
      'value' => 'weekly',
      'confidence' => 0.92,
      'tags' => ['ops'],
      'rationale' => 'user preference detected',
    ]],
  ]);
```

## Artax-Eye
- Pull context at workflow start; push adjustments after tasks.
- Use webhooks to stay synchronized with approvals.
- Respect scopes and rate limits; retry with idempotency keys.

## Best Practices
- Keep tokens short-lived and re-issue per job.
- Tag adjustments for traceability and analytics.
- Handle `429` and `409` with backoff and revision fetch.