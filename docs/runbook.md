# Operations Runbook

Guidelines for day-to-day operations, incident response, and troubleshooting.

## Day 1 Setup
- Configure env secrets for OAuth and HMAC signatures.
- Enable HTTPS, optional mTLS for critical integrations.
- Set admin roles and enable 2FA.

## Routine Maintenance
- Rotate client secrets and signing keys quarterly.
- Review rate limits and policy exceptions monthly.
- Backup database daily; run restore drills quarterly.

## Monitoring
- Alerts on token issuance spikes, `429` rates, webhook failures.
- Track queue latency and dead-letter volumes.
- Dashboard health for endpoints and approvals backlog.

## Incident Response
- Compromise suspected: revoke affected tokens; rotate keys; audit logs.
- Data issue: rollback context revision; revert memory adjustments.
- Webhook outage: pause deliveries; replay after recovery.

## Troubleshooting
- 401/403: check token validity, scopes, tenant policies.
- 409 on context: fetch latest revision and retry patch.
- 429: apply backoff; evaluate rate limits.
- Webhooks failing: verify signature, timestamp drift, retry window.

## Change Management
- Use migrations and feature flags for breaking changes.
- Document API/schema changes; version endpoints when necessary.

## Disaster Recovery
- RPO/RTO targets defined per tenant sensitivity.
- Offsite encrypted backups and tested restore procedures.