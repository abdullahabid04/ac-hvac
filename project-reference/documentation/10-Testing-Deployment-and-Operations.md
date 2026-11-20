# Testing, Deployment, and Operations Plan

## Centralized IoT HVAC Management Platform

**Version:** 2.0 \| **Status:** Proposed release guidance

## 1. Testing strategy

Testing must cover web workflows, backend rules, tenant isolation,
device integration, IR behavior, data quality, and failure recovery.

## 2. Test categories

### 2.1 Unit tests

-   Tenant context resolution.
-   Location hierarchy validation.
-   AC capability and setpoint range validation.
-   Policy inheritance and AC override.
-   Command lifecycle transitions.
-   Event deduplication.
-   State projection.
-   ON-duration calculations.
-   Timezone conversion and reporting boundaries.

### 2.2 Integration tests

-   Portal → backend command creation.
-   Backend → messaging layer publish.
-   Device acknowledgement ingestion.
-   Transmission result ingestion.
-   Physical remote event ingestion.
-   Policy update synchronization.
-   Device reassignment and credential revocation.
-   Report generation and download.

### 2.3 Security tests

-   Cross-tenant resource access.
-   IDOR attempts using foreign AC/location/device IDs.
-   Revoked device credentials.
-   Device identity spoofing.
-   Replayed/expired commands.
-   Duplicate event submission.
-   Unauthorized policy/profile changes.
-   Rate limiting and input validation.
-   Secret leakage in logs/API responses.

### 2.4 Device and IR tests

-   Repeated capture consistency.
-   Decoded and raw capture storage.
-   Frame replay.
-   Full-state frame reconstruction.
-   Supported settings combinations.
-   IR distance, angle, and line-of-sight.
-   Physical remote capture while ESP32 is installed.
-   Direct remote bypass demonstration.
-   Wi-Fi loss and reconnect.
-   Device reboot and event buffer recovery.

### 2.5 Analytics tests

-   ON/OFF interval pairing.
-   Missing OFF event.
-   Missing ON event.
-   Duplicate and out-of-order events.
-   Midnight boundary.
-   Timezone conversion.
-   Daylight-saving boundary if applicable.
-   Stale/unknown intervals.
-   Provisional ON interval.
-   Verify no kWh is calculated or displayed.

## 3. Acceptance test scenarios

  ----------------------------------------------------------------------------
  ID                      Scenario                    Expected outcome
  ----------------------- --------------------------- ------------------------
  AT-01                   Manager signs in            Access only own
                                                      organization

  AT-02                   Create                      Hierarchy persists and
                          site/building/floor/space   browses correctly

  AT-03                   Register AC and assign      Valid
                          device                      tenant/location/device
                                                      binding

  AT-04                   Submit supported ON command Command lifecycle and
                                                      transmission result
                                                      traceable

  AT-05                   Submit unsupported setting  Rejected with clear
                                                      validation reason

  AT-06                   Device reports heartbeat    Device
                                                      last-seen/connectivity
                                                      updates

  AT-07                   Capture supported remote    Classified and
                          action                      synchronized if
                                                      permitted

  AT-08                   Unknown IR signal           Logged as unknown; no
                                                      false state transition

  AT-09                   Apply inherited policy      Nearest explicit policy
                                                      wins

  AT-10                   Apply AC override           AC override takes
                                                      precedence

  AT-11                   Duplicate device event      No duplicate
                                                      state/history record

  AT-12                   Missing OFF transition      ON duration flagged
                                                      incomplete/provisional

  AT-13                   Device goes offline         Device status changes;
                                                      AC state is not
                                                      automatically OFF

  AT-14                   Cross-tenant request        Denied

  AT-15                   Review usage dashboard      Operating duration
                                                      shown; no kWh inference

  AT-16                   Restricted remote detected  Event wording is
                                                      accurate; no claim of
                                                      physical blocking
  ----------------------------------------------------------------------------

## 4. Environment strategy

Proposed environments: - Development. - Staging / hardware
integration. - Pilot. - Production.

Keep configuration separate per environment. Never reuse production
device credentials in development.

## 5. Deployment requirements

-   TLS certificates and secure ingress.
-   Secret management.
-   Database migrations and rollback plan.
-   Broker/device ACL setup if MQTT selected.
-   Unique device provisioning.
-   Logging and metrics.
-   Health checks.
-   Backup and restore.
-   Rate limits and firewall/network policy.
-   Versioned firmware/backend message compatibility.

## 6. Observability

Monitor: - API availability and latency. - Authentication failures. -
Tenant authorization failures. - Broker connectivity. - Device last-seen
and online/offline transitions. - Command publish, receipt,
transmission, failure, and expiry counts. - Event ingestion rate and
duplicate count. - Policy sync lag. - Event processing failures. -
ON-duration data completeness. - Database health, backups, and storage.

Avoid logging credentials, access tokens, or unnecessary raw IR
captures.

## 7. Backup and recovery

Define: - Database backup frequency and retention. - Restore test
cadence. - Raw IR capture backup if stored outside database. - Device
credential recovery/revocation. - Broker configuration backup. -
Recovery procedure after backend/broker outage. - Event reconciliation
after device reconnect.

## 8. Installation and commissioning checklist

For each installed AC: - Confirm location and AC identity. - Record
manufacturer/model/remote variant. - Associate ESP32 identity with AC. -
Provision unique credentials. - Confirm Wi-Fi signal and network
access. - Confirm IR transmitter line-of-sight and receiver placement. -
Validate ON/OFF and supported setpoint controls. - Validate physical
remote capture. - Confirm policy version and synchronization. - Record
firmware and IR profile version. - Record installer validation and known
limitations.

## 9. Release readiness

A pilot release is ready only when: - Critical Must requirements are
implemented or formally deferred. - Tenant isolation tests pass. -
Representative AC profiles are validated. - Command lifecycle is
traceable. - Unknown/stale states are represented correctly. - Physical
remote limitation is accepted or mitigated. - Offline/reconnect behavior
is tested. - Backup/restore and operational monitoring exist. - Client
UAT and acceptance criteria are signed off.
