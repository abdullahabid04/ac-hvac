# Implementation Plan & Checklist

**Project:** Centralized IoT HVAC Management Platform — Manager Portal
**Stack:** PHP 8.2, Laravel 12, MySQL 8.0, Blade/Tailwind/Alpine/Chart.js
**Status legend:** Not Started / In Progress / Blocked / Completed

## Phase 1 — Architecture and foundation

| ID | Task | Priority | Depends on | Expected outcome | Status |
|---|---|---|---|---|---|
| F-01 | Laravel 12 scaffold, env, MySQL connection, Vite, dependencies | P0 | — | App boots against MySQL, assets build | In Progress |
| F-02 | Design tokens, base CSS, icon set, app shell (sidebar/topbar) | P0 | F-01 | Variant 7 shell available to all screens | Not Started |
| F-03 | Authentication (session login/logout), demo users, route guards | P0 | F-01 | Managers sign in and land on Overview | Not Started |
| F-04 | Tenant context, policies, middleware, authorization helpers | P0 | F-01 | Server-side isolation primitives | Not Started |
| F-05 | Documentation: ADRs, discovery report, plan, setup | P1 | — | Engineering artifacts in `docs/` | In Progress |

## Phase 2 — Database and domain model

| ID | Task | Priority | Depends on | Expected outcome | Status |
|---|---|---|---|---|---|
| D-01 | Organizations, memberships, users | P0 | F-01 | Tenant + manager identities | Not Started |
| D-02 | Sites/buildings/floors/nested spaces | P0 | D-01 | Flexible hierarchy | Not Started |
| D-03 | AC models, IR profiles/versions/payloads | P0 | D-01 | Versioned IR catalogue | Not Started |
| D-04 | AC units, devices, assignments | P0 | D-02, D-03 | Assets and one-ESP32-per-AC registry | Not Started |
| D-05 | Commands, events, state snapshot/history | P0 | D-04 | Lifecycle + state facts | Not Started |
| D-06 | Remote policies, activity/audit, notifications, preferences | P0 | D-02 | Policy, history, alerts | Not Started |
| D-07 | Daily usage aggregates, generated reports | P1 | D-05 | Analytics + report persistence | Not Started |
| D-08 | Factories and realistic seeders (2 orgs, hierarchy, ACs, devices, history) | P0 | D-01..D-07 | Demo data + tests | Not Started |

## Phase 3 — Core backend

| ID | Task | Priority | Depends on | Expected outcome | Status |
|---|---|---|---|---|---|
| B-01 | Tenant-scoped repositories/queries + policies | P0 | D-01 | No cross-tenant access | Not Started |
| B-02 | Location hierarchy service + CRUD requests | P0 | D-02 | Validated hierarchy operations | Not Started |
| B-03 | AC asset CRUD + capability/profile validation | P0 | D-03, D-04 | Safe asset management | Not Started |
| B-04 | Device registry, assignment, token issue/revoke | P0 | D-04 | Secure device identity | Not Started |
| B-05 | Remote policy resolver (nearest explicit wins) + sync versioning | P0 | D-06 | Correct effective policy | Not Started |
| B-06 | Command service (validation, idempotency, expiry, lifecycle) | P0 | D-05 | Ordered, safe command workflow | Not Started |
| B-07 | Event ingestion (dedupe) + state projection | P0 | D-05 | Idempotent history/state | Not Started |
| B-08 | ON-duration calculator + daily aggregation | P0 | D-05, D-07 | Accurate operating hours | Not Started |
| B-09 | Notifications, activity, audit services | P1 | D-06 | Alert/audit records | Not Started |
| B-10 | Report generation (PDF/CSV) | P1 | D-07 | Real downloadable outputs | Not Started |

## Phase 4 — APIs and IoT

| ID | Task | Priority | Depends on | Expected outcome | Status |
|---|---|---|---|---|---|
| A-01 | Manager API `/api/v1`: auth, me, hierarchy, locations, ACs, commands, analytics, activity, notifications, reports, settings | P0 | B-01..B-10 | Documented JSON API | Not Started |
| A-02 | Device API: auth, heartbeat, config, pending commands, ack, transmission result, state report, remote events, policy ack | P0 | B-04..B-07 | Secure device channel | Not Started |
| A-03 | Idempotency, request IDs, rate limits, error envelope | P0 | A-01/A-02 | Consistent, safe API | Not Started |
| A-04 | Device transport abstraction + polling implementation | P1 | A-02 | Replaceable MQTT-ready layer | Not Started |
| A-05 | Development simulator (Artisan) + labelled local auto-respond | P1 | A-02 | End-to-end demo without hardware | Not Started |

## Phase 5 — Manager Portal screens

| ID | Task | Priority | Depends on | Expected outcome | Status |
|---|---|---|---|---|---|
| U-00 | Shared components: PageHeader, MetricCard, StatusBadge, RockerSwitch, TemperatureControl, ChartCard, EmptyState, Pagination, Toast | P0 | F-02 | Reusable Variant 7 components | Not Started |
| U-01 | Overview Dashboard (SCR-01) | P0 | B-08 | KPIs, charts, location summary, alerts, activity | Not Started |
| U-02 | Locations Explorer (SCR-02) | P1 | B-02 | Tree + breadcrumb + KPIs + space cards | Not Started |
| U-03 | AC Inventory (SCR-03) | P0 | B-03 | Search/filter/sort/pagination + grid/table | Not Started |
| U-04 | AC Control (SCR-04) | P0 | B-06 | Rocker, setpoint, live command lifecycle | Not Started |
| U-05 | AC Details & Status (SCR-05) | P1 | B-07 | Tabs, identity, state, events | Not Started |
| U-06 | Usage & Analytics (SCR-06) | P1 | B-08 | Duration/setpoint analytics, no kWh | Not Started |
| U-07 | Activity History (SCR-07) | P2 | B-09 | Filterable event log | Not Started |
| U-08 | Reports (SCR-08) | P2 | B-10 | Configure/generate/download | Not Started |
| U-09 | Notifications (SCR-09) | P2 | B-09 | Tabs, read/unread, deep links | Not Started |
| U-10 | Organization Settings (SCR-10) | P2 | B-05 | Org, preferences, policy, profile | Not Started |
| U-11 | Responsive behavior + accessibility pass | P1 | U-01..U-10 | Tablet/mobile usable | Not Started |
| U-12 | Visual QA against Variant 7 composite | P0 | U-01..U-10 | Screens compare directly | Not Started |

## Phase 6 — Quality and handover

| ID | Task | Priority | Depends on | Expected outcome | Status |
|---|---|---|---|---|---|
| T-01 | Feature tests: auth, isolation, IDOR, hierarchy, AC CRUD | P0 | B-01..B-03 | Green test suite | Not Started |
| T-02 | Tests: command lifecycle/expiry/idempotency, device auth, event dedupe | P0 | B-06, B-07, A-02 | Green test suite | Not Started |
| T-03 | Tests: policy inheritance/override, state freshness, ON duration | P0 | B-05, B-08 | Green test suite | Not Started |
| T-04 | Tests: notifications, reports, invalid setpoints, offline/reconnect | P1 | B-09, B-10 | Green test suite | Not Started |
| T-05 | Security review: rate limits, secrets, logs, tenant paths | P0 | All backend | No exposed secrets/IDOR | Not Started |
| T-06 | Docs: architecture, schema, API, device workflow, setup, deployment, limitations | P0 | All | Handover pack | Not Started |
| T-07 | Final acceptance run: migrate:fresh --seed, build, tests, portal smoke test | P0 | All | Verified end-to-end | Not Started |

## Risks / mitigations

| Risk | Impact | Mitigation |
|---|---|---|
| No real hardware | Cannot claim physical AC validation | Simulator explicitly labelled; docs state limitation |
| Unknown pilot IR models | Profile coverage unproven | Representative seeded profiles marked sample; validation workflow documented |
| Direct remote bypass | Policy cannot physically enforce | Clear UI copy + docs; physical mitigation noted |
| Thin analytics source data | Incomplete ON intervals | Incomplete flags, unknown not counted as OFF, provisional intervals |
| Demo credentials | Not production-safe | Documented local-only; `.env.example` has no secrets |
| Local MySQL instance | Not the user's system MySQL | Project-local instance on port 3310 documented; configurable via `.env` |
