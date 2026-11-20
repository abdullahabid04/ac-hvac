# Software Design Description (SDD)

## Centralized IoT HVAC Management Platform

**Version:** 2.0\
**Status:** Proposed logical design --- implementation choices subject
to validation

## 1. Purpose and design principles

This document describes the logical architecture and major software
design decisions. It expands the original SDD baseline without claiming
that open technical choices have been finalized.

Design principles: - Tenant isolation by default. - Modular monolith
initially, with domain boundaries. - Separate command lifecycle from
inferred AC state. - Versioned and validated IR profiles. - Idempotent
device-event ingestion. - Device-local policy/state cache for temporary
connectivity loss. - Explicit data quality and freshness. - Simple
Manager Portal with one initial role. - Avoid premature microservices
and unsupported product features.

## 2. System context

### 2.1 Main components

1.  Manager Portal --- organization-facing browser UI.
2.  Backend application/API --- authentication, authorization,
    validation, commands, events, analytics.
3.  Relational database --- organizations, users, locations, ACs,
    devices, profiles, policies, commands, events, state, audit.
4.  IoT messaging layer --- secure command and event exchange.
5.  Device registry --- identity, tenant/AC binding, provisioning,
    firmware, last-seen.
6.  ESP32 firmware --- IR capture/transmit, local policy evaluation,
    local state cache, buffering, synchronization.
7.  IR profile library --- model/protocol definitions, capabilities,
    decoded/raw representations, validation/version history.

### 2.2 Logical architecture

``` mermaid
flowchart TB
  Manager[Organization Manager]
  UI[Manager Portal]
  API[Backend Application / API]
  DB[(Relational Database)]
  Broker[Secure IoT Messaging Layer]
  Registry[Device Registry]
  ESP[ESP32 Controller per AC]
  AC[IR-Controlled Air Conditioner]
  Remote[Original Physical Remote]
  IR[Versioned IR Profile Library]

  Manager --> UI
  UI <--> API
  API <--> DB
  API <--> Registry
  API <--> IR
  API <--> Broker
  Broker <--> ESP
  ESP -->|IR transmit| AC
  Remote -->|Direct IR path| AC
  Remote -->|Captured signal| ESP
  ESP -->|IR event / heartbeat| Broker
```

The diagram is logical. Final deployment topology, cloud provider,
broker, and runtime are open.

## 3. Architectural style

**Proposed:** Modular monolith for the backend initially. Keep modules
separated by domain and service boundaries. Split into independent
services only if scale, deployment isolation, or team ownership
justifies it.

Suggested modules: - Identity and tenancy. - Organization and
settings. - Location hierarchy. - AC asset management. - Device
registry/provisioning. - IR profile catalogue. - AC control and command
orchestration. - Physical remote policy. - Device event ingestion. - AC
state and history. - Usage analytics and reports. - Notifications. -
Audit and operational monitoring.

## 4. Core workflows

### 4.1 Portal command flow

1.  Manager selects an AC and supported action.
2.  Portal sends AC ID and desired settings to backend.
3.  Backend authenticates user and enforces tenant ownership.
4.  Backend validates AC capability, setpoint range, current profile,
    and device assignment.
5.  Backend creates a command with unique ID, expiry, requester, and
    requested state.
6.  Backend publishes command to the assigned device using the selected
    secure messaging channel.
7.  ESP32 receives and validates command, resolves profile payload/state
    frame, and transmits IR.
8.  ESP32 reports receipt and transmission result if possible.
9.  Backend records lifecycle events and updates the portal.
10. Last-reported AC state changes only according to the defined
    evidence rules; transmission success alone is not physical
    confirmation.

### 4.2 Physical remote flow

1.  User presses original remote.
2.  ESP32 receiver captures signal if within placement/range.
3.  Firmware attempts decode and/or raw capture.
4.  Firmware classifies using the assigned profile; otherwise marks
    unknown.
5.  Firmware checks locally cached effective policy.
6.  If recognized and permitted, firmware updates local known
    configuration and reports event.
7.  If restricted, firmware suppresses its own
    processing/synchronization and logs a detectable attempt where
    possible.
8.  Backend authenticates device, validates event, deduplicates by event
    ID, and updates event/state history.
9.  Direct IR from the handheld remote may still reach the AC
    independently; firmware policy cannot guarantee physical blocking.

### 4.3 Policy resolution

Resolve from the most specific level upward: AC override → Space → Floor
→ Building → Site → Organization. - Enabled or Disabled is explicit. -
Inherit delegates to parent. - If no explicit value exists, apply an
approved documented default. - Disabled-by-default is a recommendation
in the source baseline, not an approved decision.

### 4.4 Offline behavior

-   Device caches latest known configuration and effective policy.
-   Device buffers eligible events durably where storage permits.
-   Retry with bounded backoff and deduplication.
-   Commands need explicit expiry and queue policy; do not execute stale
    commands after reconnect.
-   Policy changes made while device is offline must carry versions and
    reconcile on reconnect.
-   Portal marks information stale/unknown rather than presenting old
    values as current.

## 5. State and command model

### 5.1 Command lifecycle

Suggested statuses:
`created → published/queued → received → transmission_attempted → device_level_complete`
Terminal alternatives: `failed`, `expired`, `cancelled` (if cancellation
is implemented).

Do not collapse these statuses into one generic "success".

### 5.2 Device connectivity

`online`, `offline`, `unknown`, based on heartbeat threshold and last
successful message. Threshold is configurable and must be documented.

### 5.3 AC state

`known_on`, `known_off`, `unknown`, `stale`. State record should
include: - Source. - Event timestamp. - Server received timestamp. -
Confidence/quality marker. - State values and profile version where
relevant.

### 5.4 Remote event classification

-   Recognized and permitted.
-   Recognized but restricted by local policy.
-   Unknown signal.
-   Decode error / capture failure. Do not create a state transition for
    an unknown signal.

## 6. IR profile architecture

### 6.1 Profile identity

Separate manufacturer, exact AC model, remote variant, protocol, profile
version, and validation status. One model may have multiple
remote/protocol variants.

### 6.2 Representation

Support: - Decoded protocol representation where available. - Raw
pulse/space timings where required. - Payload metadata: carrier
frequency, repeat behavior, bit order, frame structure, checksum rules,
timing tolerance, and other protocol-specific attributes as needed. -
Profile capability definitions and setting ranges.

### 6.3 Full-state frames

Many AC remotes transmit complete desired-state frames rather than
isolated button codes. A profile may need: - State-to-frame encoder. -
Validated payload templates. - Field mapping for power, setpoint, mode,
fan, swing, and other supported settings. Do not assume a fixed
independent code per button.

### 6.4 Learning and validation workflow

1.  Select exact manufacturer/model/remote variant.
2.  Capture repeated signals for each relevant setting/action.
3.  Store decoded data and raw timings where possible.
4.  Compare captures for repeatability and changing fields.
5.  Identify full-state versus discrete command behavior.
6.  Map fields/actions and supported ranges.
7.  Replay in controlled conditions.
8.  Test combinations, power transitions, and edge cases.
9.  Document evidence, limitations, and profile version.
10. Publish only validated profiles; keep experimental profiles in
    draft.

## 7. Data architecture principles

-   Every tenant-owned record has tenant scope or is reachable only
    through a rigorously tenant-scoped parent relation.
-   Tenant checks occur in application authorization and query
    boundaries; do not rely only on UI filters.
-   Use recursive typed spaces for flexible physical areas.
-   Keep AC model separate from IR profile.
-   Preserve profile version used for each command/event where relevant.
-   Keep command requests, device acknowledgements, transmission
    results, remote captures, and state transitions separate.
-   Store device event time and server receipt time.
-   Use event IDs/idempotency keys.
-   Keep latest state snapshot separate from append-only history.
-   Every state update carries source, timestamp, and quality/freshness.
-   The database proposal is in `08-Database-Design-Proposal.md` and is
    intentionally adaptable.

## 8. Security architecture

-   Authenticated manager sessions.
-   Server-side tenant isolation on every resource.
-   Unique per-device identity and revocable credentials.
-   TLS for portal/backend and device/cloud transport.
-   Device messages authorized against registry and current tenant/AC
    assignment.
-   Command expiry and replay protection.
-   Rate limiting and payload validation.
-   No device secrets in browser responses.
-   Audit profile, policy, assignment, and sensitive organization
    changes.
-   Secret storage through deployment secret manager/environment
    injection, not source control.

## 9. API and messaging architecture

Conceptual user-facing endpoints and device message envelopes are
defined in `07-API-Contract-Guidelines.md`. Exact protocol/broker is
open; MQTT over TLS is a candidate, not a confirmed decision.

Device messages must be authenticated and include stable IDs,
timestamps, schema version, device identity, and correlation IDs.
Commands must include expiry and profile version/reference.

## 10. Manager Portal design

Use Variant 7 as the approved UI baseline. The ten screen purposes and
detailed specifications are maintained in the UI handoff: - Theme
Book. - UI Screen Plan. - Detailed Screen Specifications.

The Manager Portal must not expose raw IR payloads, MQTT administration,
firmware debugging, or platform-level tenant provisioning. Operational
connectivity and last-seen indicators are appropriate.

## 11. Deployment view

Proposed environments: - Development. - Staging / hardware pilot. -
Production.

Logical deployment elements: - Web frontend. - Backend application. -
Relational database. - Secure IoT broker/service. - Secret management. -
Logs/metrics/alerts. - Backup and restore. - Device provisioning and
credential revocation.

Final cloud provider, compute service, network layout, and broker remain
open.

## 12. Key technical risks

  -----------------------------------------------------------------------
  Risk                    Impact                  Mitigation
  ----------------------- ----------------------- -----------------------
  IR discovery takes      High                    Representative model
  longer                                          PoC before broad
                                                  compatibility promise

  Direct remote cannot be High                    Client decision;
  blocked                                         physical mitigation if
                                                  strict enforcement
                                                  required

  False state assumptions High                    Known/stale/unknown
                                                  model and explicit
                                                  evidence

  Wi-Fi gaps              Medium/High             Local cache, durable
                                                  buffer, retry, stale
                                                  indicators

  High event volume       Medium                  Indexing, retention,
                                                  aggregation,
                                                  partitioning only when
                                                  justified

  Device compromise       High                    Unique credentials,
                                                  TLS, revocation

  Full-state IR frames    Medium/High             Profile-specific state
                                                  encoder and raw frame
                                                  support

  Clock drift             Medium                  NTP and server receipt
                                                  timestamp

  Missing ON/OFF events   Medium/High             Incomplete intervals
                                                  and quality flags
  -----------------------------------------------------------------------

## 13. Design decisions still open

Do not treat these as finalized: - Backend language/framework and
hosting. - Database engine and final schema. - IoT broker/protocol. -
Device provisioning mechanism. - Default remote policy. - Offline
command queue semantics. - Data retention and export. - OTA firmware in
first release. - Platform support operator. - Final AC model/profile
coverage and control capabilities.
