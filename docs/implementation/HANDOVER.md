# AC/HVAC Manager Portal — Handover Documentation

**Project:** `D:\PROJECTS\WebApps\ac-hvac`
**Stack:** Laravel 12 + Blade + Tailwind CSS v4 + Alpine.js + Chart.js + MySQL 8
**UI Baseline:** Variant 7 (approved 10-screen composite)
**Environment:** Local MySQL 8 on `127.0.0.1:3310`, DB `ac_hvac` / `ac_hvac_test`, root / `ac_hvac_local`

---

## 1. Architecture overview

The portal is a server-rendered Laravel application with a single organization-admin role. All data is strictly scoped to the authenticated user's organization; there is no silent global model scope. Tenant isolation is enforced explicitly in controllers via route model binding + policy checks, and in the device API via per-device bearer tokens.

### Layers

| Layer | Responsibility |
|-------|----------------|
| Routes | `routes/web.php` (session+CSRF manager UI), `routes/api.php` (JSON manager API + device API under `v1/`) |
| Controllers | Thin HTTP layer; delegate to services; enforce authorization |
| Services | Domain logic: commands, usage, policies, ingestion, projections, reports, simulator |
| Models | Eloquent with `BelongsToOrganization` trait; UUIDs for commands/events/reports; bigint for relational entities |
| Views | Blade + shared components; design tokens in `resources/css/app.css` (`@theme`) |
| JS | `resources/js/app.js` — Alpine.js (`acControl`, toasts, confirm), Chart.js, `renderCharts()` |
| Jobs | `PublishAcCommand` (queued device publishing), `SimulateCommandLifecycle` |

### Key domain rules (non-negotiable, preserved in code)

- One ESP32 device per AC at a time (`DeviceAssignment`, `is_current`).
- Versioned IR profiles (`IrProfileVersion`, `validation_status`).
- Distinct command lifecycle facts: created → published → received → transmission_attempted → completed/failed/expired (`CommandStatus`).
- IR transmission success ≠ physical confirmation (explicitly labelled in UI and reports).
- Device online ≠ AC ON; unknown/stale ≠ OFF (`ConnectivityStatus` vs `PowerState`).
- Setpoint ≠ room temperature; ON duration ≠ kWh (explicitly stated in UI/reports).
- Remote policy: Inherit / Enabled / Disabled, nearest explicit scope wins, default = Disabled (provisional).
- Software cannot block a handheld remote's direct IR beam; policy governs synchronization/reporting only.

---

## 2. Screens implemented (all ten)

1. **Overview Dashboard** (`/overview`) — `DashboardController`
2. **Locations Explorer** (`/locations`) — `LocationsController`
3. **AC Inventory** (`/air-conditioners`) — `AcController@index`
4. **AC Control Panel** (`/air-conditioners/{ac}/control`) — `AcController@control` + `acControl` Alpine component (vertical rocker ON top/OFF bottom, circular setpoint dial, command lifecycle feedback)
5. **AC Details & Status** (`/air-conditioners/{ac}`) — `AcController@show` (tabs: Overview/History/Usage/Settings)
6. **Usage & Analytics** (`/analytics`) — `AnalyticsController@index` (period tabs, no kWh)
7. **Activity History** (`/activity`) — `ActivityController@index`
8. **Reports** (`/reports`) + `reports/pdf` — `ReportController` + `ReportService` (PDF via DomPDF, CSV)
9. **Notifications** (`/notifications`) — `NotificationController@index`
10. **Organization Settings** (`/settings`) — `SettingsController@index`

Plus: auth (`/login`, `/logout`), device API (`/api/v1/device/*`), local dev simulator (`/dev/simulator`, local env only).

---

## 3. Database schema

Migrations in `database/migrations/` (8 files). Core tables:

- `organizations`, `organization_user` (pivot: role, is_active)
- `sites`, `buildings`, `floors`, `spaces` (recursive `parent_space_id`)
- `ac_models`, `ir_profiles`, `ir_profile_versions`, `ir_profile_payloads`
- `ac_units` (bigint PK, `organization_id`, `space_id`, `ac_model_id`, `ir_profile_version_id`)
- `devices` (UUID-less bigint PK, `device_uid` unique, `api_token_hash`, `token_prefix`)
- `device_assignments` (`is_current`, one device → one AC)
- `remote_policies` (`scope_type`, `scope_id`, `policy_value`, `version`)
- `ac_commands` (UUID PK, `desired_state` JSON, `CommandStatus`, idempotency key)
- `device_events` (UUID PK, `event_uid`, `payload` JSON, idempotent by `device_id + event_uid`)
- `ac_state_snapshots` (one per AC, `quality`, `inferred` labelling)
- `ac_state_history` (append-only facts, `state_field`, `old_value`, `new_value`)
- `activity_logs`, `audit_logs`, `notifications`, `notification_preferences`
- `ac_daily_usage` (ON/unknown seconds per AC per day, no kWh)
- `generated_reports` (UUID PK, file_path, status, expires_at)

`BelongsToOrganization` trait provides `scopeForOrganization()`; no global scopes are registered.

---

## 4. APIs

### Manager API (session + CSRF, `web` middleware)
Base: `/` (web routes). JSON endpoints under `routes/web.php` return JSON for command issuance (`POST /air-conditioners/{ac}/commands`) and command polling (`GET /commands/{command}`).

### Manager JSON API (`routes/api.php`, `v1/` prefix)
- `POST /v1/auth/login`, `POST /v1/auth/logout`
- `GET /v1/me`, `/v1/overview`, `/v1/locations`, `/v1/acs`, `/v1/acs/{ac}`
- `POST /v1/acs/{ac}/commands`, `GET /v1/commands/{command}`
- `/v1/analytics`, `/v1/activity`, `/v1/notifications` (read-all / read), `/v1/reports`, `/v1/settings`, `/v1/policies`

### Device API (stateless bearer token, `device.auth` middleware, `throttle:120,1`)
Base: `/api/v1/device/`
- `POST /heartbeat` — schema_version, event_id, occurred_at (+ firmware/wifi/uptime)
- `GET /config` — device/AC/profile/policy/pending commands
- `GET /commands/pending` — queued commands for device
- `POST /commands/{command}/ack`, `/transmission-result`
- `POST /state` — event_id, occurred_at, `state` array (power: on/off, setpoint)
- `POST /remote-events` — classification, policy_result
- `POST /events` — generic event ingestion (schema_version, message_type, event_id)
- `POST /policy/ack` — effective_policy, policy_version

Device tokens are per-device, hashed with SHA-256 (`DeviceAuthService::issueToken` returns `acv1_<60 random chars>`).

---

## 5. Device workflow (simulator-backed)

1. Device authenticates with `Authorization: Bearer acv1_<token>`.
2. Device calls `POST /api/v1/device/heartbeat` periodically.
3. Device calls `GET /api/v1/device/commands/pending` to poll queued commands.
4. Device acknowledges with `POST /commands/{id}/ack` and reports `POST /commands/{id}/transmission-result`.
5. Device reports state with `POST /api/v1/device/state` (event_id, occurred_at, `state`).
6. `DeviceEventIngestionService` ingests idempotently (by `device_id + event_uid`), dedups, projects state, and derives command lifecycle facts.
7. IR transmission success updates last-known state with `inferred` quality — never physical confirmation.

Local simulator (`SimulatorService`, `SimulatorController`, `/dev/simulator`) auto-responds when `HVAC_SIMULATOR_AUTO_RESPOND=true` and `APP_ENV=local`. All simulator data is explicitly labelled `simulated`.

---

## 6. Setup & deployment

### Prerequisites
- PHP 8.2+, Composer, Node.js/npm, MySQL 8
- Databases `ac_hvac` and `ac_hvac_test` created

### Local development
```bash
cd D:\PROJECTS\WebApps\ac-hvac
cp .env.example .env   # or use existing .env (DB host 127.0.0.1:3310)
composer install
npm install
php artisan key:generate
php artisan migrate:fresh --seed
npm run build
php artisan serve
```

Demo login: `admin@northwind.test` / `password` (organization: Northwind Traders). A second organization (Contoso Facilities) is seeded for isolation tests.

### Test database
`.env.testing` targets `ac_hvac_test`. Run tests with:
```bash
vendor/bin/phpunit
```

### Production
- Set `APP_ENV=production`, `APP_DEBUG=false`
- Set `HVAC_SIMULATOR_AUTO_RESPOND=false` (simulator is local-only)
- Run `php artisan config:cache`, `route:cache`, `view:cache`
- Use a real mail driver and HTTPS; device tokens are bearer tokens — protect via TLS
- DomPDF is used for PDF reports; CSV is the Excel-compatible export

---

## 7. Testing

34 feature tests, 81 assertions, all passing (`vendor/bin/phpunit`).

Coverage areas:
- Authentication (login/logout/guest redirects)
- Tenant isolation / IDOR (cross-org AC, command, notification, report access denied)
- AC inventory, details, create, control availability
- Command lifecycle (issue → poll → status) and invalid setpoint rejection (422)
- Device bearer-token auth (valid/invalid/missing token), state report, event idempotency
- Remote policy inheritance (default disabled, org override, AC override wins, space chain)
- Settings policy persistence
- Report generation (PDF + CSV), download, cross-org download denial
- Notification read / read-all / tenant scoping

Tests use `RefreshDatabase` against `ac_hvac_test` (`.env.testing`).

---

## 8. Visual QA (Variant 7)

All ten screens reproduce the Variant 7 design language:

- Design tokens centralized in `resources/css/app.css` (`@theme`): deep navy sidebar (`#082b4c`…), icy-blue background (`#f6fbff`), white cards, professional blue (`#1689e8`), subtle cyan/amber accents.
- Compact enterprise density: 13.5px base, small labels, tight padding, `card`/`stat-card`/`metric-card` shared components.
- Sidebar (232px, fixed, deep navy, selected state with blue shadow + left indicator).
- Topbar (sticky, blurred, title/subtitle, profile dropdown, notification bell).
- Shared Blade components: `icon`, `status-badge`, `connectivity-badge`, `metric-card`, `page-header`, `empty-state`.
- **AC Control Panel** — Variant 7 vertical rocker/breaker-style switch: ON at top, OFF at bottom; clicking toggles pending position; illuminated ON (`text-blue-600` + glow), neutral OFF; pending/disabled/error states; wired to the real command workflow with lifecycle feedback (queued/received/transmission_attempted/completed/failed/expired).
- **Temperature control** — large setpoint display, −/+ buttons, circular conical-gradient dial; min/max/step come from the assigned IR profile version (never hardcoded); requested vs last-reported value clearly distinguished; labelled as target temperature, not room temperature.

All pages were smoke-tested (HTTP 200) with seeded demo data and compared against the Variant 7 composite for layout, spacing, typography, and control proportions.

---

## 9. Known limitations

- No real ESP32 hardware validation; all device behavior exercised through the local simulator only.
- Simulator auto-response is development-only (`HVAC_SIMULATOR_AUTO_RESPOND`, `APP_ENV=local`) and explicitly labelled as illustrative, not production evidence.
- Default remote policy is `Disabled`, documented as provisional pending client approval.
- ON duration is operating time derived from state transitions; it is never converted to kWh (no power measurement collected).
- PDF reports use DomPDF (local disk storage, 30-day expiry).
- No raw IR payloads, MQTT administration, firmware debugging, or developer provisioning UI is exposed in the manager portal (by design).
- No git repository initialized in the working directory.

---

## 10. File map (key files)

```
routes/web.php          Manager UI routes
routes/api.php          Manager JSON + device API routes
app/Http/Controllers/   Web + API controllers
app/Services/           Domain services (commands, usage, policy, ingestion, reports, simulator)
app/Models/             Eloquent models
database/migrations/    Schema (8 migrations)
database/factories/     Model factories (20+)
database/seeders/       DatabaseSeeder + AcCatalogueSeeder + DemoDataSeeder
resources/css/app.css   Tailwind v4 design tokens + components
resources/js/app.js     Alpine.js + Chart.js + acControl component
resources/views/        Blade layouts/views/components
tests/Feature/          34 feature tests
docs/implementation/    DISCOVERY_REPORT.md, IMPLEMENTATION_PLAN.md, this file
```

---

## 11. Checklist status

| Phase | Status |
|-------|--------|
| Discovery / Plan | Complete |
| Foundation (Laravel, deps, .env, MySQL) | Complete |
| Database (all migrations) | Complete |
| Core backend (enums, models, config, services, middleware, policies, requests, jobs) | Complete |
| APIs and IoT (manager + device, simulator) | Complete |
| Frontend (CSS, JS, shared components, layouts, all 10 screens) | Complete |
| Factories + comprehensive seeder | Complete |
| `migrate:fresh --seed` + `npm run build` | Complete |
| Feature tests (34 tests, 81 assertions, all passing) | Complete |
| Server smoke test (all 11 pages HTTP 200) | Complete |
| Handover docs | Complete |

---

**End of handover.**
