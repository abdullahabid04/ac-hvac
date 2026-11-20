# Database Design Proposal

## Centralized IoT HVAC Management Platform

**Version:** 2.0\
**Status:** Proposed conceptual/logical schema --- explicitly adaptable

## 1. Important implementation authority

This schema is a **starting proposal**, not a frozen database contract.
The coding agent may refine, normalize, split, merge, rename, add, or
remove tables/columns/indexes based on: - Validated functional
requirements. - Existing repository and ORM conventions. - Selected
relational database engine. - Expected device/event volume. - Query and
reporting patterns. - Firmware/message contract. - Transactional and
retention requirements.

The agent should preserve the domain invariants in this document,
document significant schema changes, and update migrations and schema
documentation. Do not implement this proposal mechanically if a better
design is justified.

## 2. Database approach

**Proposed:** Relational database for organization, hierarchy, AC
assets, device assignments, profile metadata, commands, policies, and
audit. Event volume may later justify partitioning, time-series storage,
or separate analytical storage, but avoid adding another database before
measured need.

Use: - Primary keys (UUID/ULID or engine-appropriate stable IDs). -
Foreign keys where practical. - Tenant scoping. - UTC timestamps. -
Explicit status enums/check constraints where supported. - JSON for
variable protocol metadata only where schema variability justifies it;
do not use JSON as a substitute for relational structure. - Soft
deletion only where business/audit requirements need it. - Migrations as
the source of schema evolution.

## 3. Conceptual entity map

``` mermaid
erDiagram
  ORGANIZATIONS ||--o{ USERS : has
  ORGANIZATIONS ||--o{ SITES : owns
  SITES ||--o{ BUILDINGS : contains
  BUILDINGS ||--o{ FLOORS : contains
  FLOORS ||--o{ SPACES : contains
  SPACES ||--o{ SPACES : nests
  SPACES ||--o{ AC_UNITS : contains
  ORGANIZATIONS ||--o{ AC_UNITS : owns
  AC_UNITS ||--o{ DEVICE_ASSIGNMENTS : assigned
  DEVICES ||--o{ DEVICE_ASSIGNMENTS : linked
  AC_UNITS }o--|| IR_MODELS : identifies
  IR_MODELS ||--o{ IR_PROFILES : has
  IR_PROFILES ||--o{ IR_PROFILE_VERSIONS : versions
  AC_UNITS ||--o{ AC_COMMANDS : receives
  DEVICES ||--o{ DEVICE_EVENTS : emits
  AC_UNITS ||--o{ AC_STATE_HISTORY : records
  AC_UNITS ||--o| AC_STATE_SNAPSHOTS : current
  ORGANIZATIONS ||--o{ REMOTE_POLICIES : configures
  AC_UNITS ||--o{ REMOTE_POLICIES : overrides
  ORGANIZATIONS ||--o{ AUDIT_LOGS : audits
```

The diagram is conceptual. Adjust cardinalities and tables to the final
implementation.

## 4. Proposed tables

### 4.1 Tenant and users

#### `organizations`

-   `id`
-   `name`
-   `slug` (unique)
-   `timezone`
-   `status`
-   `settings_json` (only non-sensitive flexible preferences)
-   `created_at`, `updated_at`

#### `users`

-   `id`
-   `name`
-   `email`
-   `password/auth identity reference`
-   `status`
-   timestamps

#### `organization_user` (or equivalent membership table)

-   `id`
-   `organization_id`
-   `user_id`
-   `role` (initially organization_admin)
-   `is_active`
-   timestamps

Even with one role, a membership model can support future organization
association. Do not build advanced RBAC prematurely.

### 4.2 Location hierarchy

#### `sites`

-   `id`
-   `organization_id`
-   `name`
-   `code`
-   `address` (optional)
-   `timezone` (optional override)
-   `status`
-   timestamps

#### `buildings`

-   `id`
-   `organization_id`
-   `site_id`
-   `name`
-   `code`
-   `status`
-   timestamps

#### `floors`

-   `id`
-   `organization_id`
-   `building_id`
-   `name`
-   `floor_number` (optional)
-   `status`
-   timestamps

#### `spaces`

-   `id`
-   `organization_id`
-   `floor_id` (or generalized parent reference if needed)
-   `parent_space_id` nullable
-   `name`
-   `code`
-   `space_type` (room, corridor, hall, gallery, section, other)
-   `status`
-   timestamps

A recursive `spaces` table supports nested spaces. If requirements later
need spaces directly under a building/floor or cross-level nesting,
consider a generalized location-node model. Choose the simplest model
that preserves referential integrity and queryability.

### 4.3 AC assets

#### `ac_units`

-   `id`
-   `organization_id`
-   `space_id`
-   `name`
-   `asset_code`
-   `manufacturer_id`
-   `ir_model_id`
-   `ir_profile_version_id` nullable until validated
-   `serial_number` optional
-   `installation_date` optional
-   `capabilities_snapshot` optional/cache only
-   `status`
-   timestamps

One AC belongs to one tenant and one physical location. Validate all
referenced records belong to the same tenant.

### 4.4 Manufacturer, model, and IR profiles

#### `ir_manufacturers`

-   `id`
-   `name`
-   `normalized_name`

#### `ir_models`

-   `id`
-   `manufacturer_id`
-   `model_name`
-   `remote_variant`
-   `protocol_name`
-   `notes`

Do not assume manufacturer/model catalogue data is tenant-owned; decide
whether catalogue records are global reference data or tenant-managed.

#### `ir_profiles`

-   `id`
-   `ir_model_id`
-   `profile_name`
-   `status` (draft, testing, validated, deprecated)
-   `active_version_id` nullable
-   timestamps

#### `ir_profile_versions`

-   `id`
-   `ir_profile_id`
-   `version_number`
-   `protocol_metadata_json`
-   `capabilities_json`
-   `temperature_min_c`, `temperature_max_c`, `temperature_step_c` where
    applicable
-   `validation_status`
-   `validation_notes`
-   `validated_by`, `validated_at`
-   timestamps

#### `ir_profile_payloads`

-   `id`
-   `ir_profile_version_id`
-   `action_key`
-   `representation_type` (decoded, raw_timings, template,
    encoder_config)
-   `payload_json` or binary/object-storage reference
-   `carrier_frequency_hz` optional
-   `repeat_metadata_json` optional
-   `checksum_metadata_json` optional
-   timestamps

Large raw captures may be stored in object storage with
metadata/reference in the relational database. Keep raw data distinct
from decoded representations.

### 4.5 Device registry and assignment

#### `devices`

-   `id`
-   `device_uid` unique
-   `credential_reference` (never plaintext secret)
-   `firmware_version`
-   `status` (unprovisioned, active, suspended, revoked)
-   `last_seen_at`
-   `last_ip` optional / privacy-reviewed
-   `wifi_rssi_dbm` optional
-   timestamps

#### `device_assignments`

-   `id`
-   `organization_id`
-   `device_id`
-   `ac_unit_id`
-   `assigned_at`
-   `unassigned_at` nullable
-   `is_current`
-   `assigned_by`
-   timestamps

Enforce at most one current device assignment per AC and, for the
initial one-device-per-AC model, at most one current AC assignment per
device. Preserve assignment history if useful.

### 4.6 Physical remote policies

#### `remote_policies`

-   `id`
-   `organization_id`
-   `scope_type` (organization, site, building, floor, space, ac)
-   `scope_id`
-   `policy_value` (inherit, enabled, disabled)
-   `version`
-   `updated_by`
-   timestamps

A polymorphic `scope_type/scope_id` is flexible but weakens foreign-key
enforcement. The coding agent may choose separate policy tables, a
location-node table, or another normalized strategy. Effective policy
resolution must follow the hierarchy and nearest explicit setting.

### 4.7 Commands and device events

#### `ac_commands`

-   `id`
-   `organization_id`
-   `ac_unit_id`
-   `device_id`
-   `requested_by`
-   `command_type`
-   `desired_state_json`
-   `ir_profile_version_id`
-   `status`
-   `issued_at`
-   `expires_at`
-   `published_at`
-   `received_at`
-   `transmission_at`
-   `completed_at`
-   `failure_code`
-   `failure_message`
-   `idempotency_key`
-   `correlation_id`
-   timestamps

Do not use one boolean `success` field as the only command result.

#### `device_events`

-   `id`
-   `event_uid` unique (or unique by device + event UID)
-   `organization_id`
-   `device_id`
-   `ac_unit_id`
-   `command_id` nullable
-   `event_type`
-   `source`
-   `classification`
-   `device_occurred_at`
-   `server_received_at`
-   `schema_version`
-   `payload_json`
-   `processing_status`
-   timestamps

#### `ac_state_snapshots`

-   `id`
-   `organization_id`
-   `ac_unit_id` unique
-   `power_state` (known_on, known_off, unknown, stale)
-   `temperature_setpoint_c` nullable
-   `operating_mode` nullable
-   `fan_setting` nullable
-   `swing_setting` nullable
-   `source`
-   `observed_at`
-   `received_at`
-   `quality`
-   `ir_profile_version_id`
-   timestamps

#### `ac_state_history`

-   `id`
-   `organization_id`
-   `ac_unit_id`
-   `device_event_id` nullable
-   `command_id` nullable
-   `source`
-   `state_field`
-   `old_value_json`
-   `new_value_json`
-   `observed_at`
-   `received_at`
-   `quality`
-   timestamps

State history should represent recognized changes, not arbitrary UI
command requests.

### 4.8 Audit, notifications, reports

#### `audit_logs`

-   `id`
-   `organization_id`
-   `actor_user_id` nullable
-   `actor_device_id` nullable
-   `action`
-   `entity_type`
-   `entity_id`
-   `before_json`
-   `after_json`
-   `occurred_at`
-   `request_id` / `correlation_id`

#### `notifications`

-   `id`
-   `organization_id`
-   `type`
-   `severity`
-   `title`
-   `message`
-   `ac_unit_id` nullable
-   `device_id` nullable
-   `event_id` nullable
-   `read_at` nullable
-   `created_at`

#### `generated_reports`

-   `id`
-   `organization_id`
-   `requested_by`
-   `report_type`
-   `filters_json`
-   `status`
-   `file_reference`
-   `created_at`
-   `completed_at`
-   `expires_at`

## 5. Relationship and integrity rules

-   Every tenant-owned entity must have a consistent `organization_id`
    or be reachable through a validated tenant-owned parent.
-   AC's space, manufacturer/model/profile, and assigned device must be
    validated before linking.
-   One current device per AC and one current AC per device in initial
    model.
-   Profile version referenced by command must be immutable once used in
    production.
-   Device event UID must be unique for idempotency.
-   Command ID and device ID must be validated together.
-   State snapshot is one current projection per AC; history is
    append-oriented.
-   Remote policy scope must resolve to a valid tenant-owned hierarchy
    node.
-   Deletion behavior for historical records must be explicit; prefer
    archive/deactivate where audit traceability matters.

## 6. Suggested indexes

Adapt to actual query plans: - Tenant + created timestamp on major
tenant-owned tables. - `ac_units(organization_id, space_id)`. -
`device_events(device_id, event_uid)` unique. -
`device_events(organization_id, ac_unit_id, device_occurred_at)`. -
`ac_commands(organization_id, ac_unit_id, issued_at)`. -
`ac_commands(device_id, status, expires_at)`. -
`ac_state_history(organization_id, ac_unit_id, observed_at)`. -
`devices(last_seen_at, status)`. - Location parent/foreign keys. -
Search indexes for AC asset code/name and location names as supported by
engine.

Do not add every possible index without measuring write overhead and
query patterns.

## 7. Time and retention

-   Persist event timestamps in UTC.
-   Preserve device time and server receipt time separately.
-   Display in organization/site timezone.
-   Define retention for raw IR captures, device events, state history,
    audit logs, and generated reports with client.
-   Consider partitioning/archival only after expected event volume and
    query requirements are known.

## 8. Data migration and evolution

-   Use reversible migrations where practical.
-   Add schema versioning for device payloads and IR profile versions.
-   Avoid destructive migrations without backup and explicit rollout.
-   Backfill data with idempotent jobs.
-   Maintain referential integrity during device reassignment.
-   Test migration against realistic pilot-scale data.

## 9. Open database decisions

-   Database engine and version.
-   UUID vs ULID vs native numeric keys.
-   Global vs tenant-specific manufacturer/model catalogue.
-   Recursive spaces vs generalized location-node table.
-   Polymorphic policy scope vs normalized per-level policy structure.
-   JSON payload vs object storage for raw IR captures.
-   Event retention and partitioning.
-   Whether device assignment history is mandatory.
-   Reporting aggregation tables/materialized views.
-   Multi-site timezone policy.

The coding agent should make these decisions based on repository,
requirements, scale, and implementation evidence, then record them in
ADRs.
