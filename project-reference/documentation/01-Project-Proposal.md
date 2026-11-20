# Project Proposal

## Centralized IoT HVAC / Air Conditioner Management Platform

**Version:** 2.0\
**Status:** Working proposal for client validation\
**Classification:** Confidential --- Client Project

## 1. Executive summary

Organizations often operate many conventional infrared-controlled air
conditioners distributed across sites, buildings, floors, rooms,
corridors, halls, galleries, and other spaces. Each AC is commonly
controlled independently through a physical remote. This makes
centralized operation, asset visibility, usage history, and operational
oversight difficult.

The proposed solution is a multi-tenant SaaS platform combining a
web-based Manager Portal, backend services, a relational data store, an
IoT communication layer, and one ESP32-based controller per AC. Each
ESP32 has an IR transmitter and receiver, connects over Wi-Fi, sends
supported commands to its assigned AC, and captures signals from the
original physical remote where compatible.

The platform provides centralized inventory and location management,
supported AC control, device connectivity visibility, remote-operation
synchronization, hierarchical physical-remote policy, event history, and
estimated ON duration. Electricity consumption measurement is explicitly
excluded from the current scope.

## 2. Problem statement

The current decentralized operating model creates several problems: - No
unified view of AC assets across an organization. - Managers must visit
locations or rely on individual remotes to operate ACs. - No consistent
history of ON/OFF operations or setpoint changes. - Physical remote
actions are not centrally visible. - Multiple AC brands and remote
variants make a universal command implementation unreliable. - Device
connectivity and AC operating state are easily confused. - Management
cannot compare operating duration across locations from a single portal.

## 3. Proposed solution

Install one ESP32 controller per AC. The controller communicates with
the cloud over Wi-Fi and interfaces with the AC using IR.

The Manager Portal allows an organization administrator to: - Manage the
physical location hierarchy. - Register AC assets and associate
model/profile information. - View AC state as last reported/known,
separately from ESP32 connectivity. - Send supported ON/OFF and
temperature commands. - Review physical remote events when detected and
supported. - Configure inherited physical-remote policies and AC-level
overrides. - Review event history and operating-duration analytics. -
Receive device and command alerts. - Generate operational reports.

## 4. Objectives

1.  Centralize AC inventory and operational visibility.
2.  Provide supported remote control through assigned ESP32 devices.
3.  Support multiple manufacturers through exact model/remote-specific
    IR profiles.
4.  Capture and classify original remote signals where feasible.
5.  Synchronize recognized and permitted remote actions to the backend.
6.  Maintain timestamped command, device, and AC-state history.
7.  Calculate ON duration from recognized ON/OFF transitions and flag
    incomplete intervals.
8.  Apply hierarchical physical-remote policy with individual AC
    overrides.
9.  Isolate organization data in a multi-tenant architecture.
10. Deliver an initial release with one organization administrator role
    and a focused feature set.

## 5. Stakeholders

  -----------------------------------------------------------------------
  Stakeholder                         Interest / responsibility
  ----------------------------------- -----------------------------------
  Client organization                 Owns deployment, AC inventory,
                                      locations, policies, and acceptance

  Energy / Power Management           Primary Manager Portal users

  Owner / Management                  Organization-level operational
                                      visibility

  Software team                       Portal, backend, data,
                                      integrations, deployment

  Hardware / installation team        ESP32 installation, Wi-Fi
                                      provisioning, IR placement and
                                      validation

  Product owner                       Resolves scope, policy defaults,
                                      and open product decisions
  -----------------------------------------------------------------------

## 6. Scope

### 6.1 In scope for the product baseline

-   Multi-tenant organization structure and server-side tenant
    isolation.
-   One initial administrator/manager role.
-   Organization → Site → Building → Floor → nested Space → AC
    hierarchy.
-   AC inventory and manufacturer/model/remote variant association.
-   One ESP32 per AC in the initial deployment model.
-   Secure device identity and connectivity/last-seen reporting.
-   Versioned IR profiles with decoded and/or raw timing
    representations.
-   Supported portal command workflow.
-   Physical remote capture/classification/synchronization where
    supported.
-   Inherited physical-remote policy and AC overrides.
-   Command and event lifecycle tracking.
-   Last-known AC state and state freshness.
-   ON/OFF history and ON-duration calculations.
-   Temperature setpoint and mode history where supported.
-   Manager dashboard, inventory, locations, control, details,
    analytics, activity, reports, notifications, and settings.
-   Security, audit, testing, deployment, and handover documentation.

### 6.2 Excluded from current scope

-   Electricity metering, kWh measurement, billing, and electricity-cost
    calculation.
-   Complex role-based access control and granular employee permissions.
-   Guaranteed physical AC-state confirmation using one-way IR alone.
-   Universal compatibility with all brands/models without validation.
-   Native mobile application.
-   Scheduling, occupancy sensing, geofencing, and AI optimization
    unless separately approved.
-   Platform super-admin interface as part of the Manager Portal.
-   Advanced firmware/IR engineering screens for organization managers.

## 7. Proposed deliverables

1.  Organization-facing web Manager Portal.
2.  Multi-tenant backend and application services.
3.  Relational database and migration/seed strategy.
4.  ESP32 device communication and command/event integration.
5.  Device registry and AC assignment.
6.  Versioned IR profile catalogue and validation workflow.
7.  Physical-remote policy resolution and synchronization.
8.  Event/state history and operating-duration reporting.
9.  API and device-message contracts.
10. Test suite, deployment setup, operational monitoring, installation
    guide, and handover package.

## 8. Success criteria

The initial release should demonstrate that: - An administrator can sign
in and access only their organization. - The administrator can create
and browse the agreed location hierarchy. - AC assets can be registered,
located, and associated with a model/profile. - Each AC can be assigned
to its ESP32 device. - A supported portal command reaches the correct
device and its IR transmission attempt is recorded. - Supported
physical-remote events can be captured, classified, and synchronized
when permitted. - Policy inheritance and AC overrides resolve
correctly. - Event history is traceable by AC, location, timestamp, and
source. - ON duration is calculated with incomplete-data indicators. -
Device connectivity is distinct from AC state. - No electricity
consumption is presented as measured or inferred from ON duration. -
Tenant isolation and device identity tests pass.

## 9. Feasibility and key limitations

### 9.1 IR compatibility

Different models and remote variants may use different protocols and
frame formats. Some AC remotes transmit a complete desired-state frame
rather than a single independent button code. A proof of concept must
validate exact representative models before broad compatibility is
promised.

### 9.2 Physical remote limitation

The ESP32 receiver is a listener, not an inline gate. A handheld remote
can transmit directly to the AC without passing through the ESP32.
Software can control how the ESP32 processes a captured signal, but
cannot guarantee that the AC ignores a direct remote signal. Strict
blocking requires a separate physical enforcement approach and client
approval.

### 9.3 State confidence

IR transmission success does not prove that the AC executed the command.
The system must distinguish command request, device receipt,
transmission result, and last-known/inferred AC state. Optional feedback
hardware may be considered if verified physical state is required.

### 9.4 Usage metrics

ON duration is derived from recognized state transitions. Missing events
can create incomplete intervals. This metric is not electricity
consumption.

## 10. Delivery approach

  -----------------------------------------------------------------------
  Phase                   Focus                   Exit outcome
  ----------------------- ----------------------- -----------------------
  0                       Discovery and           Approved workflows,
                          requirement validation  model inventory, open
                                                  decisions

  1                       IR / ESP32 proof of     Representative
                          concept                 capture/transmit
                                                  feasibility

  2                       Backend foundation      Tenant, auth,
                                                  hierarchy, assets,
                                                  device registry,
                                                  messaging

  3                       Portal foundation       Login, overview,
                                                  locations, inventory,
                                                  AC details

  4                       IR profiles and control End-to-end control for
                                                  validated models

  5                       Physical remote         Policy-aware capture
                          synchronization         and event sync

  6                       History and analytics   Event timeline, ON
                                                  duration, setpoint/mode
                                                  history

  7                       Hardening and pilot     Security, resilience,
                                                  UAT, deployment and
                                                  handover

  8                       Expansion               Additional profiles and
                                                  separately approved
                                                  capabilities
  -----------------------------------------------------------------------

Do not commit calendar estimates until representative AC models,
firmware ownership, integration responsibilities, hosting, client
availability, and pilot scope are known.

## 11. Open decisions

-   Exact AC brands, models, and remote variants in the pilot.
-   Whether physical remote restriction requires physical enforcement or
    detection/logging is sufficient.
-   Default physical-remote policy when no level has an explicit value.
-   Exact MVP control capabilities per validated profile.
-   Device messaging protocol, broker, and hosting.
-   Firmware ownership, provisioning, manufacturing, and field support.
-   Pilot hardware availability and installation responsibilities.
-   Expected organizations, sites, ACs, and event volume.
-   Offline command queue behavior and expiry.
-   Event retention and export requirements.
-   Whether a platform support operator is needed.
-   Timezone, language, report formats.
-   OTA firmware update scope for first release.
-   Acceptance criteria for successful control without physical
    feedback.

## 12. Recommendation

Proceed with a staged end-to-end MVP. Validate IR feasibility and
physical-remote limitations before committing to broad AC compatibility
or implementing the complete portal. Use the approved Variant 7 UI
design baseline for the Manager Portal and maintain explicit records of
all decisions that remain open.
