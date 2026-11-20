# Engineering Implementation Guide

## Coding-Agent Instructions --- Centralized IoT HVAC Platform

**Version:** 2.0\
**Audience:** Autonomous coding agent / engineering team

## 1. Mission

Implement the product described in the Proposal, SRS, and SDD while
following the Variant 7 UI handoff. This guide is
implementation-oriented. It is not a replacement for product
requirements.

## 2. Mandatory first actions

Before editing code: 1. Inspect the repository structure, README,
dependency manifests, existing architecture, tests, and deployment
files. 2. Identify existing framework/runtime versions; do not upgrade
major dependencies without reason. 3. Inspect existing conventions,
authentication, authorization, migrations, API patterns, and UI
component library. 4. Identify existing code that already satisfies
requirements. Extend it instead of duplicating modules. 5. Create or use
a dedicated feature branch. 6. Produce a short repository assessment and
implementation plan before large changes. 7. Do not assume the stack
proposed in this document is already selected. Repository evidence and
client constraints take precedence, subject to requirements.

## 3. Source-of-truth order

When documents appear inconsistent: 1. Latest explicit client decision /
approved change record. 2. SRS functional and non-functional
requirements. 3. SDD architecture and domain rules. 4. UI Theme Book and
screen specifications for visual behavior. 5. This implementation guide
and specialist guides. 6. Existing repository conventions where they do
not conflict with approved requirements.

Never silently reinterpret a confirmed requirement. Raise conflicts and
record a decision.

## 4. Confirmed product constraints to preserve

-   Multi-tenant SaaS with strict server-side tenant isolation.
-   Initial manager role only; complex RBAC deferred.
-   One ESP32 per AC in initial model.
-   ESP32 Wi-Fi plus IR transmitter and receiver.
-   Multiple exact AC model/remote variants; profile validation
    required.
-   Original remote capture and synchronization where supported.
-   Hierarchical policy Inherit / Enabled / Disabled; nearest explicit
    wins; AC override.
-   ON/OFF history, ON duration, temperature setpoint history, mode
    history where supported.
-   No kWh/electricity measurement in current scope.
-   Command request, device receipt, IR transmission result, and
    last-reported AC state are separate.
-   Device online does not mean AC ON.
-   Temperature is setpoint, not room temperature.
-   IR cannot guarantee physical execution.
-   ESP32 software cannot block a direct handheld remote IR beam.
-   Variant 7 is the approved Manager Portal UI baseline.
-   Locations Explorer must be hierarchy + cards, not a CAD/Sweet Home
    3D floor-plan editor.

## 5. Technology selection policy

The previous SDD recommends a modular monolith and relational database.
MQTT over TLS is a candidate. These are proposals, not final mandates.

The agent may select or revise: - Frontend framework and component
library. - Backend language/framework. - Relational database engine. -
IoT broker/protocol. - Queue/cache technology. - Hosting/deployment
topology. - Schema details and indexes.

Selection must consider existing repository, team capability, ESP32
library compatibility, operational cost, security, maintainability, and
validated requirements. Record major decisions in
`docs/decisions/ADR-xxxx.md` or equivalent.

Do not introduce microservices, Kubernetes, multiple databases, or
complex infrastructure without a demonstrated requirement.

## 6. Implementation sequence

### Phase 0 --- Repository and requirements validation

-   Map modules and existing code.
-   Confirm MVP and open decisions.
-   Identify exact AC test hardware and remote variants.
-   Verify firmware/API responsibilities.
-   Create requirement-to-module traceability.

### Phase 1 --- Hardware/IR feasibility

-   Validate receive/transmit with representative AC models.
-   Determine raw/decoded representation and full-state frame behavior.
-   Test remote capture placement and line-of-sight.
-   Confirm direct-remote limitation with client.
-   Do not promise broad compatibility before validation.

### Phase 2 --- Platform foundation

-   Authentication and organization membership.
-   Tenant context and authorization middleware/policies.
-   Organization settings and timezone.
-   Audit framework.
-   Database migration baseline.
-   API error conventions and request validation.

### Phase 3 --- Locations and AC inventory

-   Hierarchy with nested typed spaces.
-   AC asset CRUD.
-   Manufacturer/model/remote variant metadata.
-   Device registry and AC assignment.
-   Inventory search/filtering.

### Phase 4 --- Device messaging and command lifecycle

-   Secure device identity.
-   Device heartbeat and last-seen.
-   Command record and expiry.
-   Publish/receive/transmit acknowledgement lifecycle.
-   Device event ingestion with idempotency.
-   Offline and retry behavior.

### Phase 5 --- IR profile library and control

-   Versioned profile catalogue.
-   Capabilities and supported ranges.
-   Capture/decoded/raw payload representation.
-   Profile validation workflow.
-   ON/OFF and setpoint commands for validated profiles.
-   Never use unvalidated payloads as production commands.

### Phase 6 --- Physical remote synchronization and policy

-   Policy inheritance resolver.
-   AC-level override.
-   Policy versioning and device synchronization.
-   Firmware local cache.
-   Recognized/permitted, restricted, unknown, decode-failure event
    classes.

### Phase 7 --- State, history, analytics

-   Latest state snapshots.
-   Append-only history.
-   Event-source attribution.
-   ON-duration calculations and incomplete intervals.
-   Setpoint/mode history.
-   Date/location/AC filters.

### Phase 8 --- Manager Portal

Implement using the separate Variant 7 UI package. Recommended order: 1.
Shared shell, theme tokens, navigation. 2. Overview. 3. AC Inventory. 4.
AC Control. 5. Locations Explorer. 6. AC Details. 7. Usage & Analytics.
8. Activity History. 9. Reports. 10. Notifications. 11. Organization
Settings.

### Phase 9 --- Hardening and pilot

-   Tenant isolation and security tests.
-   Device credential lifecycle.
-   Offline/reconnect tests.
-   Duplicate/out-of-order event tests.
-   Timezone and midnight boundary tests.
-   UAT with representative AC models.
-   Deployment, backup/restore, monitoring, and handover.

## 7. Code quality requirements

-   Keep domain logic out of UI components/controllers.
-   Use explicit request validation and authorization.
-   Use typed DTOs/schemas or equivalent for device/API messages.
-   Keep external device payloads versioned.
-   Use transactions for multi-record operations where required.
-   Make event ingestion idempotent.
-   Use stable identifiers and correlation IDs.
-   Add automated tests for critical business rules.
-   Avoid duplicated policy/state/command logic.
-   Do not store secrets in source code.
-   Avoid overengineering and premature abstractions.
-   Keep documentation synchronized with implemented behavior.

## 8. Required engineering artifacts

Maintain: - Architecture decision records (ADRs). - Environment variable
example without secrets. - Database migrations and schema
documentation. - API contract / OpenAPI where applicable. - Device
message schema and examples. - Local development setup. - Test plan and
test execution evidence. - Deployment and rollback instructions. -
Backup/restore procedure. - Known limitations and open decisions.

## 9. Change authority

The agent may improve database schema and implementation details based
on requirements and repository evidence. It may not unilaterally remove
tenant isolation, command lifecycle distinction, physical remote
limitation, state freshness, event traceability, or other Must
requirements. Any requirement-level change needs product-owner/client
approval.

## 10. Definition of done

A feature is not done merely because its UI renders. It must include: -
Validated requirements mapping. - Authorization and tenant isolation. -
Validation and error handling. - Loading/empty/error/stale states. -
Automated tests for business logic and critical flows. -
Logging/observability where operationally relevant. - Documentation
update. - No misleading claim about AC physical state or electricity
usage.
