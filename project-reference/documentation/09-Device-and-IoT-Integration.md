# Device and IoT Integration Guide

## ESP32 / IR / Cloud Integration

**Version:** 2.0 \| **Status:** Logical requirements and proposed
patterns

## 1. Device model

Initial model: one ESP32 controller per AC. Each controller includes: -
Wi-Fi connectivity. - IR transmitter. - IR receiver. - Unique device
identity. - Secure device credentials. - Local copy of latest known AC
configuration and effective remote policy. - Local event buffer/retry
capability subject to hardware storage constraints.

## 2. Device responsibilities

-   Connect securely to the selected IoT service.
-   Report heartbeat and firmware version.
-   Receive commands for its assigned AC.
-   Validate command identity, expiry, profile version, and supported
    action.
-   Resolve/encode the IR frame using the validated profile.
-   Transmit IR.
-   Report command receipt and transmission result separately.
-   Capture original remote signals.
-   Classify supported signals or preserve unknown/decode-failure
    outcomes.
-   Apply latest local remote policy to its own
    processing/synchronization.
-   Maintain local known configuration and buffer events during
    temporary outages.
-   Reconcile policy/state after reconnect.

## 3. IR capture and transmit

### Capture

-   Ensure receiver placement can capture both original remote and
    relevant signals.
-   Capture repeated samples.
-   Store decoded protocol data where possible.
-   Preserve raw pulse/space timings when needed.
-   Track capture quality and timestamps.
-   Do not infer an action from an unknown signal.

### Transmit

-   Use profile-specific encoding or validated payload template.
-   Validate AC profile version and capability.
-   Account for carrier frequency, frame structure, repeats, timing, and
    protocol-specific requirements.
-   Record transmission attempt and outcome.
-   Never label transmission as physical confirmation.

### Full-state AC frames

Many AC remotes encode complete desired state. A setpoint update may
need to preserve power, mode, fan, swing, and other fields.
Firmware/profile logic must not assume each remote button maps to one
independent code.

## 4. Remote policy behavior

Policy values: Inherit, Enabled, Disabled. Device receives effective
policy plus version.

When permitted: - Recognized signal can update local known
configuration. - Device reports a physical-remote event.

When restricted: - Firmware may ignore its own captured signal for
synchronization/processing. - Log a restricted attempt only if detection
is technically possible. - The original remote may still directly
control the AC.

The UI and client documentation must clearly state that software cannot
block the direct remote-to-AC IR path.

## 5. Connectivity and offline behavior

-   Device heartbeat determines connectivity.
-   Online threshold is configurable.
-   During Wi-Fi loss, use local policy/configuration cache.
-   Buffer events durably where feasible.
-   Retry with bounded backoff.
-   Include stable event IDs to prevent duplicates.
-   Do not execute expired queued commands after reconnect.
-   Reconcile policy versions and report stale local policy.
-   Use NTP when available and include device and server timestamps.

## 6. Device identity and provisioning

-   Unique per-device credentials; no fleet-wide shared secret.
-   Secure provisioning and association to one tenant/AC.
-   Credential rotation/revocation process.
-   Device assignment changes must be authorized and audited.
-   Device messages must be checked against registry assignment.
-   Avoid embedding long-lived secrets in firmware images shared across
    devices.

## 7. Suggested device state model

Connectivity: - Online - Offline - Unknown

Command: - Received - Rejected - Transmission attempted - Transmission
failed - Expired

Remote capture: - Recognized/permitted - Recognized/restricted -
Unknown - Decode error

Policy: - Effective enabled/disabled - Policy version - Last
synchronized time - Sync status

## 8. Hardware proof-of-concept checklist

For each pilot AC/remote variant: - Record manufacturer, exact model,
remote model/variant. - Confirm IR receiver/transmitter electrical
requirements and placement. - Capture power, temperature, mode, fan,
swing signals as relevant. - Repeat captures to assess consistency. -
Determine full-state frame behavior. - Test replay at realistic
distance/angle. - Test state changes through original remote. - Test
ESP32 capture and portal synchronization. - Test Wi-Fi loss, reboot,
reconnect, event replay. - Demonstrate direct remote bypass
limitation. - Document profile version, supported capabilities, and
known failures.

## 9. Firmware/backend compatibility

-   Use versioned device message schemas.
-   Maintain compatibility matrix between firmware and backend schema
    versions.
-   Reject unsupported schema versions safely.
-   Add contract tests using representative message fixtures.
-   Roll out firmware changes in stages.
-   OTA firmware update for first release remains an open scope
    decision.
