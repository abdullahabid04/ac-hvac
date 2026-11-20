# API Contract Guidelines

## Manager Portal and Device Integration

**Version:** 2.0 \| **Status:** Conceptual contract --- finalize after
stack and transport selection

## 1. API principles

-   Use versioned API namespace, e.g. `/api/v1`.
-   JSON for manager-facing HTTP APIs unless an approved alternative
    exists.
-   Authenticated user endpoints and authenticated device endpoints must
    be separated.
-   Tenant scope is resolved and enforced server-side.
-   Use consistent validation and error envelopes.
-   Use stable IDs, pagination, filtering, and explicit timestamps.
-   Use idempotency keys for retryable command-creation operations.
-   Device messages must be schema-versioned and authenticated.

## 2. Conceptual user-facing endpoints

These are illustrative paths, not finalized contracts.

  ----------------------------------------------------------------------------------------
  Method                  Path                                     Purpose
  ----------------------- ---------------------------------------- -----------------------
  POST                    `/api/v1/auth/login`                     Manager authentication

  POST                    `/api/v1/auth/logout`                    End session

  GET                     `/api/v1/me`                             Current user and
                                                                   organization context

  GET                     `/api/v1/organization`                   Organization
                                                                   profile/settings

  GET                     `/api/v1/hierarchy`                      Retrieve location tree

  POST                    `/api/v1/sites`                          Create site

  POST                    `/api/v1/buildings`                      Create building

  POST                    `/api/v1/floors`                         Create floor

  POST                    `/api/v1/spaces`                         Create nested typed
                                                                   space

  GET                     `/api/v1/locations/{id}`                 Location detail and
                                                                   summary

  GET                     `/api/v1/locations/{id}/acs`             ACs in selected
                                                                   location

  GET                     `/api/v1/acs`                            List/search/filter ACs

  POST                    `/api/v1/acs`                            Register AC

  GET                     `/api/v1/acs/{id}`                       AC detail and
                                                                   last-known state

  PATCH                   `/api/v1/acs/{id}`                       Update AC metadata

  POST                    `/api/v1/acs/{id}/commands`              Request supported
                                                                   command

  GET                     `/api/v1/commands/{id}`                  Command lifecycle

  GET                     `/api/v1/acs/{id}/events`                AC event history

  GET                     `/api/v1/acs/{id}/usage`                 ON duration and setting
                                                                   history

  GET                     `/api/v1/analytics/overview`             Organization summary

  GET                     `/api/v1/analytics/usage`                Usage aggregation

  GET                     `/api/v1/devices`                        Device registry/health
                                                                   for authorized operator
                                                                   scope

  GET                     `/api/v1/locations/{id}/remote-policy`   Read
                                                                   effective/configured
                                                                   policy

  PUT                     `/api/v1/locations/{id}/remote-policy`   Update location policy

  PUT                     `/api/v1/acs/{id}/remote-policy`         Set AC override

  GET                     `/api/v1/notifications`                  List notifications

  PATCH                   `/api/v1/notifications/{id}`             Update read state

  GET                     `/api/v1/reports`                        List generated reports

  POST                    `/api/v1/reports`                        Request report
                                                                   generation
  ----------------------------------------------------------------------------------------

Do not expose device provisioning, raw IR profile administration, or
firmware diagnostics in the organization Manager Portal unless product
scope explicitly expands.

## 3. Query conventions

Suggested list query parameters: - `page`, `per_page` - `q` - `site_id`,
`building_id`, `floor_id`, `space_id` - `status` - `from`, `to` -
`source` - `sort`, `direction`

Validate all filter IDs against tenant scope.

## 4. Suggested response envelope

``` json
{
  "data": {},
  "meta": {
    "request_id": "request-uuid"
  }
}
```

Suggested error envelope:

``` json
{
  "error": {
    "code": "VALIDATION_ERROR",
    "message": "The requested value is not supported.",
    "details": {},
    "request_id": "request-uuid"
  }
}
```

Use appropriate HTTP status codes. Avoid returning stack traces or
secrets.

## 5. Command request example

``` json
{
  "action": "set_state",
  "desired_state": {
    "power": "on",
    "temperature_setpoint_c": 24
  },
  "client_request_id": "client-generated-uuid"
}
```

Backend resolves the active profile and device. The client must not
submit arbitrary IR payloads.

Suggested command response:

``` json
{
  "data": {
    "command_id": "command-uuid",
    "status": "created",
    "expires_at": "2026-11-07T09:30:00Z"
  }
}
```

## 6. Device message envelope

All device messages should include: - `schema_version` - `message_id` or
`event_id` - `device_id` - `occurred_at` (device timestamp) - `sent_at`
where useful - `message_type` - `correlation_id` for command-related
messages - Typed payload

### 6.1 Command message

``` json
{
  "schema_version": 1,
  "message_type": "ac.command",
  "command_id": "command-uuid",
  "device_id": "device-uuid",
  "ac_id": "ac-uuid",
  "profile_id": "profile-uuid",
  "profile_version": 3,
  "action": "set_state",
  "desired_state": {
    "power": "on",
    "temperature_setpoint_c": 24
  },
  "issued_at": "2026-11-07T09:25:00Z",
  "expires_at": "2026-11-07T09:30:00Z"
}
```

### 6.2 Device acknowledgement

``` json
{
  "schema_version": 1,
  "message_type": "command.ack",
  "event_id": "event-uuid",
  "command_id": "command-uuid",
  "device_id": "device-uuid",
  "received_at": "2026-11-07T09:25:02Z",
  "result": "accepted"
}
```

### 6.3 IR transmission result

``` json
{
  "schema_version": 1,
  "message_type": "command.transmission_result",
  "event_id": "event-uuid",
  "command_id": "command-uuid",
  "device_id": "device-uuid",
  "transmitted_at": "2026-11-07T09:25:03Z",
  "result": "transmitted",
  "error_code": null
}
```

`transmitted` means the ESP32 reports an IR transmission attempt/result.
It is not physical AC confirmation.

### 6.4 Physical remote event

``` json
{
  "schema_version": 1,
  "message_type": "remote.signal",
  "event_id": "event-uuid",
  "device_id": "device-uuid",
  "captured_at": "2026-11-07T09:26:00Z",
  "classification": "recognized",
  "policy_result": "permitted",
  "recognized_action": "set_state",
  "decoded_data": {},
  "raw_capture_reference": null
}
```

Do not put large raw timing arrays into the message broker by default.
Upload or store them through an authenticated capture workflow/reference
if needed.

### 6.5 Heartbeat

``` json
{
  "schema_version": 1,
  "message_type": "device.heartbeat",
  "event_id": "event-uuid",
  "device_id": "device-uuid",
  "occurred_at": "2026-11-07T09:27:00Z",
  "firmware_version": "1.0.0",
  "uptime_seconds": 86400,
  "wifi_rssi_dbm": -61
}
```

### 6.6 Policy update

``` json
{
  "schema_version": 1,
  "message_type": "policy.update",
  "device_id": "device-uuid",
  "policy_version": 12,
  "effective_policy": "disabled",
  "issued_at": "2026-11-07T09:28:00Z"
}
```

## 7. Messaging transport

MQTT over TLS is a proposed candidate, not a confirmed decision. If
selected: - Use unique credentials/certificates per device. - Restrict
topic permissions by device identity. - Separate command and
telemetry/event topics. - Define QoS, retained-message policy, expiry,
reconnect, and offline queue behavior. - Do not retain stale commands in
a way that allows unintended execution. - Validate broker ACLs and
revocation behavior.

## 8. API security

-   User APIs: session/token authentication plus tenant authorization.
-   Device channel: device identity and credential validation.
-   Device ID in payload is not sufficient authentication.
-   Validate command IDs, expiry, profile version, and assignment.
-   Rate-limit command creation and login.
-   Prevent replay and duplicate event processing.
-   Redact secrets and raw credentials from logs.

## 9. Contract governance

Before production, create an OpenAPI specification for HTTP endpoints
and a versioned JSON Schema/typed contract for device messages. Add
compatibility tests between backend and firmware. Breaking message
changes require a schema version and rollout plan.
