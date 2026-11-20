# Backend Implementation Guide

## Centralized IoT HVAC Management Platform

**Version:** 2.0 \| **Architecture direction:** Modular monolith
proposed

## 1. Responsibilities

The backend is responsible for: - Authentication and organization
context. - Server-side tenant authorization. - Location hierarchy and AC
asset management. - Device registry and assignment. - IR profile
metadata and capability validation. - Command creation, routing, expiry,
and lifecycle. - Device message authentication and event ingestion. -
Physical remote policy resolution. - AC state snapshots and history. -
ON-duration calculations and reporting. - Notifications and audit
records.

## 2. Suggested domain modules

1.  Identity/Tenancy.
2.  Organization/Settings.
3.  Locations.
4.  AC Assets.
5.  Device Registry.
6.  IR Profiles.
7.  AC Commands.
8.  Physical Remote Policies.
9.  Device Events.
10. AC State.
11. Usage Analytics.
12. Reports.
13. Notifications.
14. Audit/Operations.

Keep domain rules in services/use-cases rather than controllers.

## 3. Tenant isolation

-   Resolve authenticated user's organization context on the server.
-   Every tenant-owned query must be scoped.
-   Validate tenant ownership for nested IDs (AC, location, device,
    profile, event).
-   Never trust a tenant ID supplied by the browser as authorization.
-   Device messages must resolve tenant/AC from the authenticated device
    registry record, not from untrusted payload fields alone.
-   Add cross-tenant negative tests for every major resource domain.

## 4. Suggested service boundaries

-   `TenantContextResolver`
-   `LocationHierarchyService`
-   `AcAssetService`
-   `DeviceRegistryService`
-   `IrProfileResolver`
-   `AcCapabilityValidator`
-   `AcCommandService`
-   `DeviceMessagePublisher`
-   `DeviceEventIngestionService`
-   `RemotePolicyResolver`
-   `PolicySynchronizationService`
-   `AcStateProjectionService`
-   `OnDurationCalculator`
-   `NotificationService`
-   `AuditService`

Names are illustrative; align with the selected framework and repository
conventions.

## 5. Command orchestration

1.  Validate manager authentication and tenant.
2.  Load AC within tenant.
3.  Validate active device assignment.
4.  Resolve active validated IR profile/version.
5.  Validate action and parameter range against capabilities.
6.  Create command record with unique ID, requested state, requester,
    created time, expiry, and correlation ID.
7.  Publish to device channel.
8.  Record publish/queue outcome.
9.  Accept device receipt acknowledgement.
10. Accept transmission result separately.
11. Update AC last-known state only under approved inference rules.
12. Notify portal using polling, SSE, WebSocket, or selected transport.

Use idempotency for command submission where retry is possible. Define
duplicate-command behavior. Expired commands must not execute after
reconnect.

## 6. Device event ingestion

-   Authenticate device connection.
-   Verify device is active and assigned.
-   Validate schema version, message size, timestamps, and required
    fields.
-   Deduplicate by device ID + event ID (or globally unique event ID).
-   Persist raw/normalized event as appropriate.
-   Record device event time and server receipt time.
-   Project recognized event into state history/snapshot.
-   Preserve unknown/decode-error events without inventing state
    changes.
-   Trigger analytics/notification updates asynchronously where
    appropriate.

## 7. Policy resolution

Hierarchy: Organization → Site → Building → Floor → Space → AC override.

Resolution: - Start at AC override. - If Inherit, inspect parent
Space. - Continue Floor, Building, Site, Organization. - First explicit
Enabled/Disabled wins. - If no explicit setting, use configured default;
current recommendation Disabled but open for client approval.

Cache effective policy with version. Device stores latest received
policy and applies it locally. A stale policy must be observable.

## 8. State projection

Keep separate: - Command record. - Device acknowledgement. - IR
transmission result. - Recognized physical remote event. - AC state
snapshot. - AC state history.

Every state projection should include source, timestamp, quality, and
profile context. Device online status must not update AC state by
itself.

## 9. ON-duration calculation

-   Sort recognized ON/OFF transitions by event time.
-   Pair each ON with the next valid OFF.
-   Clip intervals to requested reporting window.
-   If latest known state is ON without a matching OFF, either show a
    provisional interval to report time or mark incomplete according to
    approved reporting policy.
-   Unknown/stale gaps must be flagged.
-   Handle midnight, daylight-saving/timezone boundaries,
    duplicate/out-of-order events, and corrections.
-   Do not convert duration to kWh.

## 10. Reliability and resilience

-   Use bounded retries and exponential backoff where suitable.
-   Commands have expiry.
-   Event ingestion is idempotent.
-   Buffer device events locally where firmware storage permits.
-   Reconcile after reconnect.
-   Use NTP and server receipt timestamps.
-   Expose health metrics for broker, ingestion, command failures, and
    device last-seen.

## 11. Security

-   Unique revocable device credentials.
-   TLS for device/cloud and browser/API.
-   Secure password/session handling.
-   Rate-limit commands and login.
-   Validate command capabilities and payloads.
-   Protect against replay using IDs, timestamps, expiry, and device
    identity.
-   Never expose secrets to frontend.
-   Audit policy, profile, device assignment, and privileged changes.

## 12. Backend tests

-   Tenant isolation and cross-tenant IDOR attempts.
-   Hierarchy parent/child constraints.
-   One-device-per-AC assignment rules.
-   Profile capability validation.
-   Command lifecycle and expiry.
-   Device authentication and revoked credentials.
-   Event deduplication and out-of-order arrival.
-   Policy inheritance and AC override.
-   ON-duration calculations and incomplete intervals.
-   Timezone conversion.
-   Remote unknown/restricted event behavior.
