# MASTER IMPLEMENTATION PROMPT
## Centralized IoT HVAC / Air Conditioner Management Platform

You are acting as a senior software architect and full-stack implementation engineer. You are responsible for delivering a working organization-facing Manager Portal and its supporting Laravel backend, MySQL database, APIs, and IoT integration foundation.

## 1. Mission and non-negotiable outcome

Implement the complete product described in the supplied reference package. This is not a static mockup, a dashboard-only exercise, or a backend-only task.

The client-selected stack is:
- PHP
- Laravel
- MySQL

Inspect the actual repository before making changes. Respect the installed PHP/Laravel versions, existing dependencies, conventions, and architecture unless a justified change is necessary.

The product includes:
- Organization-facing web Manager Portal.
- Authentication and organization membership.
- Multi-tenant isolation.
- Location hierarchy.
- AC inventory and control.
- IR profile association and command orchestration.
- ESP32 device registry and secure device APIs.
- Event/state history.
- Physical remote policy inheritance.
- Usage-duration analytics.
- Activity history, notifications, reports and exports.
- Tests, seeders, setup documentation and deployment guidance.

**Do not stop after reading documents or preparing a plan. Once discovery and planning are complete, start implementation without waiting for another approval.**

## 2. Reference discovery is mandatory

The complete reference package is in `project-reference/` inside the Laravel project root (or in the supplied reference directory if named differently).

Before editing code:
1. Inspect the repository root and identify the actual reference directory.
2. Recursively discover all files in the reference directory.
3. Read every documentation file relevant to the product.
4. Inspect every UI design document.
5. Open and inspect the actual Variant 7 image, not merely its filename or textual description.
6. Inspect the current Laravel application, composer.json, PHP version, frontend tooling, routes, middleware, authentication, models, migrations, tests, and deployment files.
7. Identify existing code that should be reused.
8. Record missing requirements, contradictions, assumptions and open decisions.

Do not alter or delete supplied reference files.

### Required reading order

Read the documentation index, proposal, SRS, SDD, implementation guide, and relevant specialist guides. Then read the Variant 7 theme book, screen plan, and detailed screen specifications. Inspect the actual approved image:

`project-reference/ui-design/variant-7/variant-7-approved-10-screen-composite.png`

## 3. Source-of-truth rules

Use this precedence:
1. Latest explicit client decision.
2. SRS and confirmed product requirements.
3. SDD and domain constraints.
4. Variant 7 theme book and detailed screen specifications for visual behavior.
5. Implementation and specialist guides.
6. Existing repository conventions where they do not conflict with approved requirements.
7. Engineering judgment for unresolved details.

The SRS/SDD define product behavior. The mockup is the visual reference; its sample names, counts, dates and metrics are illustrative, not production facts.

If documents conflict, record the conflict and chosen interpretation. Do not silently discard confirmed requirements.

## 4. Variant 7 visual fidelity

Variant 7 is the approved visual baseline. Reproduce it as closely as practical.

Inspect and match:
- Sidebar width, color, spacing and selected navigation state.
- Header proportions and controls.
- Main content width, page margins and vertical rhythm.
- Icy-blue page background and white cards.
- Deep navy navigation.
- Professional blue actions and subtle cyan highlights.
- Typography, font sizes, weights and line heights.
- Card dimensions, borders, shadows and radii.
- Tables, filters, buttons, badges, charts, icons and empty states.
- The visual hierarchy and compact enterprise information density.

Use the theme book's tokens. Centralize tokens and reusable components. Do not invent a different design language for individual pages.

Do not use a CAD/Sweet Home 3D floor-plan editor as the Locations Explorer. Variant 7 uses a hierarchy plus location/space summary cards.

Avoid excessive gradients, glassmorphism, neon colors, oversized decoration, unnecessary animations and inconsistent spacing.

### Core AC control component

Implement the custom vertical rocker/breaker-style ON/OFF switch shown in Variant 7:
- ON at the top and OFF at the bottom.
- Clicking changes the selected/pending position.
- Clear illuminated ON indication and neutral OFF indication.
- Pending, disabled, error and unavailable states.
- Connect it to the real backend command workflow.
- Do not replace it with a generic checkbox or ordinary toggle if that changes the reference design.

### Temperature control

Implement the large setpoint display and minus/plus controls:
- Initial increment: 1°C.
- Limits must come from the applicable AC/IR profile.
- Never hardcode one range for all models.
- Show pending command state and last-known setpoint.
- Clearly distinguish requested value from last reported/known value.
- Temperature is the AC setpoint, not measured room temperature.

### Visual QA

After implementing each major screen, run it and compare it directly with the matching Variant 7 reference. Fix differences in proportions, spacing, typography, colors, cards, controls and density. Do not claim visual matching without performing this comparison.

## 5. Product context and domain

Organizations have multiple conventional IR-controlled ACs across sites, buildings, floors, rooms, corridors, halls, galleries, sections and other spaces.

Initial hardware model:
- One ESP32 per AC.
- Each ESP32 has an IR transmitter and IR receiver.
- ESP32 communicates over Wi-Fi.
- Original physical remotes remain usable when policy permits.
- ESP32 captures supported remote signals, transmits compatible commands, caches local configuration/policy and reports events.

The system is a multi-tenant SaaS product. Every organization's data must be isolated server-side.

### Location hierarchy

Support:

Organization → Sites → Buildings → Floors → Spaces → ACs.

Spaces must be flexible and nestable; do not assume all areas are rooms or that hierarchy has a fixed depth.

### Initial user role

The first portal role is Organization Administrator / Manager. Complex RBAC is deferred. A full platform-owner/developer administration portal is not part of this implementation scope.

## 6. Confirmed technical constraints

Preserve all of these:
- Multiple AC manufacturers/models are supported through exact, validated IR profiles.
- Many AC remotes transmit full-state frames, not independent button codes.
- Store decoded data and raw timings where available; support profile-specific state-to-frame encoding where required.
- Command requested, backend accepted/queued, device received, IR transmission result, and last-known AC state are distinct facts.
- IR transmission success does not prove the AC physically executed the command.
- ESP32 online status does not prove AC ON.
- AC state may be known, stale or unknown.
- Unknown periods must not be counted as confirmed OFF time.
- Temperature is setpoint, not room temperature.
- ON duration is not kWh or electricity consumption.
- Electricity/kWh monitoring is outside the current scope.
- ESP32 software cannot block the direct IR beam from a handheld remote to the AC.
- Remote permission supports Inherit / Enabled / Disabled, with nearest explicit policy winning and individual AC override.
- Preserve device event time and server receipt time.
- Use stable event IDs and idempotent ingestion.
- Keep local device configuration/policy for supported offline behavior and buffer events for later reporting.

## 7. IR profiles and command workflow

Implement profile-aware command resolution.

Profile data may include:
- Manufacturer, model and remote variant.
- Protocol.
- Decoded command/state representation.
- Raw pulse timings.
- Temperature limits and supported steps.
- Supported modes/fan/swing capabilities where applicable.
- State-to-frame encoder or validated templates.
- Version, validation status and evidence/limitations.

Do not assume every model uses the same protocol or independent button codes.

A command workflow should:
1. Authenticate the manager.
2. Verify tenant membership and AC access.
3. Validate the requested action and profile capability.
4. Resolve the assigned device and applicable profile version.
5. Create a command record with stable ID, expiry and correlation data.
6. Deliver or queue the command using the selected transport.
7. Track device receipt/acknowledgement.
8. Track IR transmission attempt/result separately.
9. Update last-known state only with appropriate source and confidence.
10. Surface pending, success, timeout and failure states to the portal.

Use retries, expiry, idempotency and safe duplicate handling.

## 8. Physical remote synchronization and policy

When a physical remote is used:
1. ESP32 receives the signal.
2. Firmware attempts decode/raw capture.
3. It classifies supported actions using the applicable profile.
4. It checks its locally cached effective remote policy.
5. When permitted and recognized, it updates local known configuration and reports an event.
6. Backend authenticates the device event, deduplicates it, records history and updates last-known state as appropriate.
7. Unknown or unsupported signals are handled safely and visibly as unknown where useful.

Policy levels: organization, site, building, floor, space and AC. Values: Inherit, Enabled, Disabled. Nearest explicit setting wins.

Important: because the handheld remote beam travels directly to the AC, software at the ESP32 cannot physically prevent that beam from reaching the AC. Do not claim otherwise. Strict physical blocking requires an additional physical installation/enforcement method.

## 9. Required Manager Portal screens

Implement all ten screens. AC Control and AC Details are contextual routes.

1. **Overview Dashboard**
   - Total ACs, known/reported ON, known/reported OFF, unknown/stale, online/offline devices, today's ON duration.
   - AC state distribution, operating-hours trend, location/building summary, active alerts and recent activity.
   - Filters must scope relevant widgets.
   - No kWh metrics.

2. **Locations Explorer**
   - Flexible nested hierarchy.
   - Left hierarchy navigation and right selected-location panel.
   - Breadcrumb, location summary KPIs and simple space cards.
   - Space cards show name, AC count and known ON/OFF/unknown summary.
   - No CAD or editable floor-plan editor.

3. **Air Conditioners Inventory**
   - Search, filters, sorting, pagination, grid/table view.
   - AC name/ID, location, manufacturer/model, reported state, setpoint, today's ON duration, device connectivity, last update and actions.
   - Unknown setpoint is Unknown/---, never 0°C.

4. **AC Control Panel**
   - Variant 7 rocker switch and temperature control.
   - AC identity/location, connectivity, last update, last-known state, command status and pending/error feedback.
   - Initial controls: ON/OFF and temperature setpoint.

5. **AC Details & Status**
   - AC metadata, location, manufacturer/model, assigned ESP32, connectivity/last-seen, last-known configuration, recent commands, physical remote events and timeline.

6. **Usage & Analytics**
   - ON duration by day, AC, building, floor and space.
   - ON/OFF patterns and setpoint trends.
   - Today, yesterday, 7 days, 30 days and custom ranges.
   - Clearly indicate incomplete/stale/unknown data.
   - No electricity consumption or kWh.

7. **Activity History**
   - Timestamp, AC, location, action, source, result, user/device.
   - Sources: portal, physical remote, ESP32/device, backend/system.
   - Search and filters.

8. **Reports**
   - Daily/weekly/monthly usage.
   - Building/floor operating hours.
   - AC ON/OFF history.
   - Setpoint history.
   - Device connectivity where useful.
   - PDF/Excel export where practical.

9. **Notifications**
   - Device offline/reconnected.
   - Command delivery failure/timeout.
   - Stale AC state.
   - Physical remote activity.
   - Unknown IR signal.
   - Read/unread, mark read, navigate to affected resource.

10. **Organization Settings**
    - Organization information and timezone.
    - Notification preferences.
    - Physical remote policy overview.
    - Basic account/profile settings.
    - No raw IR payloads, MQTT administration, firmware debugging or developer provisioning UI.

## 10. Laravel backend

Inspect the actual application before choosing patterns or adding dependencies.

Inspect:
- PHP and Laravel versions.
- composer.json and installed packages.
- Routes, middleware and authentication.
- Existing models, migrations and database setup.
- Frontend build tooling and components.
- Tests and deployment configuration.

Use a maintainable Laravel architecture with appropriate:
- Controllers.
- Form Requests.
- Models and relationships.
- Services/actions.
- Policies and middleware.
- API Resources.
- Jobs, events, listeners and notifications.
- Transactions and queues where justified.

Keep complex business logic out of controllers. Avoid unnecessary microservices or infrastructure complexity.

### Backend domains

- Organizations and memberships.
- Locations.
- AC assets.
- Device registry.
- IR profiles.
- Commands and delivery attempts.
- AC state snapshots and history.
- Remote policies.
- Device events.
- Usage aggregation.
- Activity/audit.
- Notifications.
- Reports.

## 11. MySQL database

Read `08-Database-Design-Proposal.md`. It is adaptable, not frozen.

You may improve normalization, relationships, indexes, data types and table structure. Preserve domain invariants and document significant deviations.

Cover suitable entities for:
- Organizations, users and memberships.
- Sites/buildings/floors/spaces.
- ACs and AC model metadata.
- ESP32 devices and assignments.
- IR profiles and versions.
- Commands and delivery attempts.
- State snapshots and append-only history.
- Remote policies.
- Device events.
- Usage aggregates.
- Activity/audit logs.
- Notifications and preferences.

Use foreign keys, unique constraints, indexes, timestamps, appropriate deletion rules and tenant-scoped query paths. Avoid excessive JSON for relational data; JSON is acceptable for protocol-specific payloads and extensible metadata.

Keep current state separate from historical events. Preserve profile version used for commands/events where relevant.

## 12. APIs and IoT integration

Implement documented, validated APIs for:
- Device authentication.
- Heartbeats.
- Configuration retrieval.
- Command retrieval/delivery.
- Command acknowledgement.
- IR profile retrieval.
- Device event ingestion.
- State/configuration reporting.
- Physical remote event reporting.

Also implement manager-facing application endpoints.

Use consistent HTTP methods/status codes, validation, authorization, rate limits, request IDs, idempotency and safe error responses. Keep device and manager APIs appropriately separated. Do not expose raw database structures.

Transport/broker choice remains open unless the repository/client has decided it. MQTT over TLS is a candidate, not a confirmed mandate. Record major architecture decisions.

### Device identity and offline behavior

Each device needs unique identity and revocable credentials. Authenticate device messages against registry and current tenant/AC assignment. Use TLS for production communication.

ESP32 should cache relevant configuration, profile and remote policy. Buffer events during outages and retry after reconnection. Preserve event IDs and original device timestamps. Keep device event time and server receipt time separately; use NTP where available.

### Development simulator

If hardware is unavailable, build a development-only simulator that can generate:
- Heartbeats and online/offline transitions.
- Command reception and acknowledgements.
- IR transmission success/failure.
- Physical remote events.
- State/configuration reports.
- Delayed event delivery.

Label simulated data. Keep simulator behavior separate from production device integration. Never claim hardware validation from simulator tests.

## 13. Frontend behavior and reusable components

Build the full portal, not just static screenshots.

Use the existing frontend stack and conventions. Create shared components for:
- Application shell/sidebar/header.
- Page headers and breadcrumbs.
- KPI cards and chart cards.
- AC status badges.
- Inventory rows/cards.
- Location hierarchy and space cards.
- Rocker switch.
- Temperature control.
- Filters/search/pagination.
- Notification items.
- Loading skeletons.
- Empty/error/stale states.
- Confirmation dialogs and toast feedback.

All controls must work. Implement search, filters, sorting, pagination, validation, create/edit flows, confirmations, loading, empty, error and success feedback.

For AC commands:
1. Show pending state.
2. Send request.
3. Display backend/device command lifecycle.
4. Handle success, timeout and failure.
5. Do not claim physical AC state was verified without evidence.

Desktop-first, tablet-responsive. Do not compromise the approved desktop composition unnecessarily for mobile.

## 14. Usage calculations

Calculate ON duration from known state transitions and event history. Handle missing transitions, offline gaps, stale state, unknown state, time zones and incomplete reporting.

Do not treat unknown intervals as OFF. Do not present ON duration as measured electricity consumption.

## 15. Security and quality

Implement:
- Server-side tenant isolation on every resource and background job.
- Authentication and authorization.
- Secure per-device credentials and revocation.
- TLS-ready communication.
- Input validation and rate limiting.
- Command expiry/replay protection.
- Audit logs for sensitive changes.
- Secret management through environment/deployment secret injection.
- No device secrets in browser responses.
- Safe IR payload handling.
- Database transactions and error logging.

Avoid giant controllers, duplicated logic, hardcoded tenant/device IDs, hardcoded temperature ranges, fake production claims, unprotected APIs and frontend-only authorization.

## 16. Development seeders

Create realistic but modest development data:
- Multiple organizations.
- Sites, buildings, floors and varied spaces.
- Multiple ACs and manufacturers/models.
- Associated ESP32 devices.
- Online/offline devices.
- Known ON/OFF/unknown states.
- IR profiles.
- Commands and device events.
- Usage history.
- Remote policies.
- Notifications.

Use factories and small test datasets. Clearly label simulated/sample data. Document a development login if appropriate.

## 17. Required execution phases

### Phase 0 — Discovery
- Inspect repository, versions, dependencies, architecture and tests.
- Find and read every reference document.
- Inspect all Variant 7 images.
- Identify reusable code, gaps, conflicts and open decisions.
- Produce a concise discovery report.

### Phase 1 — Plan and checklist
Create a practical implementation roadmap and checklist with:
- Task ID.
- Task.
- Priority.
- Dependencies.
- Expected outcome.
- Status (Not Started / In Progress / Blocked / Completed).

Include architecture, database, backend, API, frontend, IoT, testing, security, deployment and risks. Save it separately from supplied references (for example `docs/implementation/IMPLEMENTATION_PLAN.md`).

**Immediately continue to implementation after this phase. Do not wait for approval.**

### Phase 2 — Foundation
Configure environment, verify dependencies, establish design tokens/shared shell, authentication, DB connection, route structure and tenant authorization foundation.

### Phase 3 — Database
Finalize schema, migrations, models, relationships, indexes, factories and seeders. Verify on a clean database.

### Phase 4 — Core backend
Implement organizations, memberships, locations, AC inventory, device registry, IR profiles, remote policies, commands, state and event history.

### Phase 5 — APIs and IoT
Implement device authentication, heartbeat, configuration, command workflow, acknowledgements, event ingestion, state reports, remote events, retries/idempotency and simulator.

### Phase 6 — Frontend
Implement shared shell and all ten screens, connecting them to real backend functionality.

### Phase 7 — Analytics and reporting
Implement ON duration, usage aggregation, filters, setpoint history, notifications, report generation and exports.

### Phase 8 — Visual refinement
Compare each screen with Variant 7 and fix layout, spacing, typography, colors, controls and responsive behavior.

### Phase 9 — Testing/security
Run backend/frontend tests, tenant isolation tests, API tests, command and offline scenarios, event deduplication, policy inheritance, usage calculations and security checks.

### Phase 10 — Handover
Document actual architecture, schema, modules, APIs, device workflows, setup, environment variables, seeders, test commands, deployment, known limitations and outstanding work.

## 18. Test coverage

Test at least:
- Authentication and organization isolation.
- Authorization and cross-tenant access denial.
- Location hierarchy.
- AC CRUD and device assignment.
- IR profile association and capabilities.
- Command lifecycle and failure states.
- Device authentication and heartbeat.
- Event ingestion and duplicate events.
- Remote policy inheritance and AC overrides.
- Unknown/stale state handling.
- ON duration calculations and missing events.
- Notifications and report filters.
- Invalid setpoints and unsupported actions.
- Offline/reconnect behavior.
- Frontend command pending/error feedback.

Run tests continuously, not only at the end.

## 19. Progress reporting

Update the implementation checklist as work progresses.

After each major phase, report:
- Completed work.
- Files/modules created or changed.
- Important decisions and deviations.
- Tests run and results.
- Remaining tasks.
- Blockers.

A file existing does not prove a feature works. Verify behavior before marking complete. Mark partial work honestly. Distinguish simulator validation from real hardware validation.

## 20. Final acceptance

The work is complete only when:
- The Laravel application runs locally with MySQL.
- Authentication and tenant isolation work.
- Locations and AC inventory work.
- Device registry and IR profile associations work.
- Commands and delivery lifecycle are tracked.
- State confidence/freshness is represented correctly.
- Remote policy inheritance works.
- Device events and activity history are recorded.
- All ten Manager Portal screens work.
- Usage duration and setpoint history work without invented kWh.
- Notifications and reports work.
- Variant 7 visual comparison has been performed and refined.
- Automated tests pass or remaining failures are documented.
- Seeders and setup instructions work.
- API and deployment documentation reflect the actual implementation.

## 21. Start now

Begin by inspecting the repository and the complete `project-reference/` directory. Read all documentation and inspect the approved Variant 7 image. Produce the discovery report, implementation roadmap and checklist. Then immediately start implementing the application phase by phase.

Do not stop at planning. Do not build only a dashboard. Do not build a platform-owner admin portal. Deliver the complete organization-facing Manager Portal and its supporting PHP + Laravel + MySQL application.

Prioritize correctness, security, maintainability, product completeness and close visual fidelity to Variant 7.
