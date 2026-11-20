# Phase 0 — Discovery Report

**Project:** Centralized IoT HVAC / Air Conditioner Management Platform
**Date:** 2026-11-07
**Reference baseline:** Documentation Pack v2.0, UI baseline Variant 7 (approved)
**Repository state at discovery:** no existing application code; only `project-reference/`.

## 1. Repository and environment inspection

| Item | Finding |
|---|---|
| Application code | None. The working directory contained only `project-reference/`. |
| PHP | 8.2.29 (CLI, ZTS, VS16 x64) |
| Composer | 2.8.11 |
| Node / npm | v24.14.1 / 11.11.0 |
| MySQL | MySQL Server 8.0.43 and 9.0 services installed. Existing service credentials were unavailable to the agent. A dedicated project-local MySQL 8.0 instance was provisioned on `127.0.0.1:3310` with database `ac_hvac` (see `docs/setup/LOCAL_SETUP.md`). |
| PHP extensions | `pdo_mysql`, `mysqli`, `mbstring`, `intl`, `gd`, `zip`, `openssl` available. |
| Existing conventions to reuse | None; Laravel 12 conventions apply. |

## 2. Reference pack reviewed

Read in the mandated order:

- `documentation/00` through `10` (proposal, SRS, SDD, implementation, frontend, backend, API, database, IoT, testing/deployment).
- `ui-design/variant-7/01-theme-book.md`, `02-ui-screen-plan.md`, `03-screen-specifications.md`.
- The actual approved composite image `variant-7-approved-10-screen-composite.png` was opened and inspected.

### Observed Variant 7 visual baseline

- Deep-navy persistent sidebar (~240 px at desktop) with snowflake wordmark.
- Nav order: Dashboard/Overview, Locations, Air Conditioners, Usage & Analytics, Activity History, Reports, Notifications, Settings.
- Compact top bar: page title and subtitle at left; site/date filters; bell; avatar.
- Icy-blue page background (`#F6FBFF` family) with subtle cloud/wave motifs; white cards.
- KPI cards with tinted icon squares, large value, small status/percentage sub-label.
- Blue/cyan charts; green for reported ON; neutral grey for known OFF; amber for warning/stale; red failure.
- AC inventory cards: asset code, state pill, small model/location row, setpoint with snowflake, ON duration with clock; blue selected outline.
- Control screen: AC illustration, vertical rocker (ON top, OFF bottom), circular setpoint gauge with minus/plus controls.
- Locations Explorer: hierarchy tree at left, summary KPI cards and simple space cards at right. No CAD/floor-plan editor.
- Overall compact enterprise density, 10–14 px card radii, restrained shadows.

## 3. Confirmed domain truths (preserved)

1. Multi-tenant, server-side isolation on every resource and job.
2. One organization administrator/manager role in MVP.
3. Organization → Site → Building → Floor → nested Space → AC.
4. One ESP32 per AC; IoT credentials unique and revocable.
5. Exact model/remote-variant IR profiles, versioned and validated.
6. Command requested, backend accepted/published, device receipt, IR transmission result, and last-known AC state are distinct facts.
7. IR transmission success is not physical confirmation.
8. Device online status is not AC ON status.
9. AC state can be known, stale or unknown; unknown is not OFF.
10. Temperature is the AC setpoint, not room temperature.
11. ON duration is operating time, not kWh/electricity.
12. Physical remote policy: Inherit / Enabled / Disabled, nearest explicit wins, AC override.
13. ESP32 software cannot block the direct handheld-remote IR beam.
14. Preserve device event time and server receipt time; idempotent ingestion.
15. Device caches configuration/policy and buffers events when offline.

## 4. Decisions taken for this implementation (documented as ADRs)

| # | Decision | Rationale |
|---|---|---|
| D1 | Laravel 12 + Blade + Tailwind CSS v4 + Alpine.js + Chart.js | Matches client-selected PHP/Laravel/MySQL stack, server-rendered tenant-scoped portal, low operational complexity. |
| D2 | Modular monolith with typed domain services | SDD proposes modular monolith; no microservices justified. |
| D3 | MySQL 8.0, bigint keys for relational entities, UUIDs for commands/events/reports | Stable external IDs where lifecycle/idempotency matter; simple eager relationships elsewhere. |
| D4 | Explicit tenant scoping + policies (no silent global scope) | Avoids accidental leaks in jobs/device API and keeps behavior testable. |
| D5 | Device authentication with per-device hashed bearer tokens | Unique, revocable credentials; no shared secret; no secrets returned to the browser. |
| D6 | REST polling for device command delivery in this build (MQTT-ready abstraction) | No broker was confirmed in the reference. A `DeviceMessagePublisher` abstraction keeps transport replaceable. |
| D7 | Recursive `spaces` table, fixed parent tables for site/building/floor | Preserves the required hierarchy and referential integrity while allowing flexible nested typed spaces. |
| D8 | Polymorphic remote-policy scope validated in application/services | Matches the hierarchy levels; DB-level polymorphic FKs are not possible in MySQL, so tenant/scope validation is enforced in `RemotePolicyResolver`/requests. |
| D9 | `ac_state_snapshots` for current state; append-only `ac_state_history` for transitions; daily aggregate table | Follows SDD separation and enables fast dashboards plus auditable history. |
| D10 | PDF via DomPDF + CSV (Excel-compatible) exports | Practical, locally verifiable; avoids heavy spreadsheet dependency. |
| D11 | Development simulator available as Artisan command and local auto-responder | Required because no ESP32 hardware exists; explicitly labelled simulated. |
| D12 | Default remote policy when no explicit value: **Disabled**, documented as an unapproved recommendation | SRS FR-046 says the default must be documented/approved; `Disabled` is the reference recommendation. Configuration is centralized in `config/hvac.php`. |
| D13 | Demo notifications/activity are seeded sample data, clearly marked in UI/docs | Reference data is illustrative; UI must not claim production facts. |

## 5. Conflicts and ambiguities recorded

- **Default physical-remote policy:** SRS says Disabled is a proposal, not confirmed. Implemented as `Disabled` default but centralized and documented as provisional.
- **Command as state evidence:** IR success is not confirmation. We update last-known state only as `inferred` quality with explicit UI labeling ("Last known, inferred from command; not physically verified"), never as reported/confirmed.
- **Device transport:** reference says MQTT is a candidate, not mandated. Implemented authenticated REST polling behind a transport abstraction; MQTT remains documented as future.
- **Organization catalogue vs global IR profiles:** chosen as global reference data (seeded), with tenant-owned AC/profile assignment. Prevents duplicate protocol catalogue per tenant.
- **Nested spaces under site/building directly:** not supported; spaces always hang from a floor. This matches the required hierarchy. A generalized location-node model is noted as future if cross-level nesting is required.
- **Reports format:** PDF and CSV delivered; native XLSX is documented as future.

## 6. Gaps / risks carried forward

- No real ESP32 hardware validation is possible in this environment. All device behavior is simulator-validated only.
- Exact pilot AC models/remotes are unknown; seeded IR profiles are representative samples and marked as such.
- Direct remote IR cannot be blocked by software; the UI and docs state this clearly.
- Production TLS termination, broker ACLs, secret manager, backup/restore targets, event retention and OTA remain deployment-time decisions.

## 7. Reusable code identified

None (empty repository). The implementation follows Laravel 12 conventions and adds domain-specific services, policies, resources and shared Blade components.
