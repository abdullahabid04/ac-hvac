# AC HVAC IoT Device API Documentation

This document describes the device-facing API for the AC HVAC Management Platform.
It is intended for ESP32-based IoT devices (and software simulators) that
communicate with the Laravel backend to receive commands and report state.

**This documentation describes the actual implemented API.** It was written
after backend authentication, device endpoints, simulator integration, and
end-to-end testing were all completed and verified.

---

## A. Introduction

### Purpose

The device API allows ESP32-based IoT devices to:

- Identify themselves to the backend using a registered serial number
- Retrieve device configuration (AC assignment, IR profile, remote policy)
- Poll for pending commands issued from the Manager Dashboard
- Acknowledge command receipt
- Report IR transmission results
- Report AC state (power, temperature setpoint, etc.)
- Report physical remote control events
- Acknowledge remote policy changes

### System Architecture

```
ESP32 Device  <====>  Laravel Backend (API v1)  <====>  Manager Dashboard
   |                        |                        |
   |  X-Device-Serial header|                        |
   |  (serial-number auth)  |                        |
   |                        |                        |
   +---- poll commands -----+                        |
   +---- report state ------+                        |
   +---- report events ------+                       |
                                                     +---- issue commands (manager auth)
```

### Communication

All communication is over HTTPS/HTTP JSON. The device initiates every request;
the backend never pushes to the device. Commands are delivered via polling.

### Device Identification Mechanism

Devices are identified exclusively by their **registered serial number**, sent
as the `X-Device-Serial` HTTP header on every request. No bearer token, no
`Authorization` header, and no API key is required.

Serial numbers follow the format established by `DeviceSerialService`:

```
HVAC-ESP32-YYYY-NNNNNN
```

Example: `HVAC-ESP32-2026-000001`

### Supported Device Operations

- Power control (AC ON / AC OFF)
- Temperature setpoint adjustment
- Operating mode, fan, and swing control (profile-dependent)
- Command acknowledgement and transmission reporting
- Periodic heartbeat / state reporting
- Physical remote event reporting
- Remote policy acknowledgement

---

## B. Authentication

### Serial-Number-Only Authentication

Device authentication is **serial-number-only**. Token-based device
authentication (bearer tokens, `Authorization` headers, API keys) is **not
required** and is not used by device endpoints.

### How the Device Identifies Itself

The device sends its registered serial number in the `X-Device-Serial` HTTP
header on every request:

```
X-Device-Serial: HVAC-ESP32-2026-000001
```

The serial number may also be provided as a JSON body field
`device_serial_number` on POST/PUT requests, but the header is the recommended
method and works for all HTTP methods (including GET).

### Required Headers

| Header | Value | Required |
|--------|-------|----------|
| `Content-Type` | `application/json` | Yes (for requests with a body) |
| `Accept` | `application/json` | Yes |
| `X-Device-Serial` | Registered device serial number | Yes |
| `Authorization` | **Not required** | No |

### Authentication Validation

The backend validates the serial number as follows:

1. **Format check** — The serial must match the pattern
   `^HVAC-ESP32-\d{4}-\d{6}$` (e.g. `HVAC-ESP32-2026-000001`).
2. **Existence check** — A device with that serial number must exist.
3. **Active check** — The device's `status` must be `active`.

### Responses for Invalid/Missing Serial

| Scenario | HTTP Status | Error Code |
|----------|-------------|------------|
| Missing serial number | 401 | `DEVICE_SERIAL_REQUIRED` |
| Invalid serial format | 401 | `DEVICE_SERIAL_INVALID` |
| Unknown or inactive serial | 401 | `DEVICE_NOT_FOUND` |

---

## C. Base URLs

| Environment | Base URL | Usage |
|-------------|----------|-------|
| Local Testing | `http://127.0.0.1:8000` | Development / local Laravel backend |
| Production | `https://hvac.cosmosalgos.com` | Production HVAC platform |

All device endpoints are under the `/api/v1` prefix.

Example full endpoint:
```
http://127.0.0.1:8000/api/v1/device/config
https://hvac.cosmosalgos.com/api/v1/device/state
```

### Environment Configuration (Python Simulator)

The Python simulator uses a single Boolean switch:

```python
USE_PRODUCTION = False  # Local testing
USE_PRODUCTION = True   # Production
```

---

## D. Common Request Conventions

| Convention | Detail |
|------------|--------|
| HTTP Methods | `GET` for retrieval (config, pending commands); `POST` for submissions (heartbeat, ack, state, events) |
| Content-Type | `application/json` |
| Serial Number Placement | `X-Device-Serial` header (or `device_serial_number` body field on POST) |
| Timestamps | ISO 8601 UTC, e.g. `2026-11-21T03:44:22+00:00` |
| Event IDs | Unique string, max 80 characters, e.g. `hb-ESP32-AC-LECTURE-01-1234567890` |
| Command IDs | UUIDs returned by the backend |
| Temperature | Integer degrees Celsius |
| Power | String `"on"` or `"off"` |
| Schema Version | Integer `1` |

### Request Validation

All device endpoints validate the request envelope:
- `schema_version` must be `1`
- `message_type` must be one of the supported types
- `event_id` is required (string, max 80 chars)
- `occurred_at` is required (valid date)

---

## E. Every Device API Endpoint

### E.1 Device Configuration Retrieval

**Purpose:** Get device info, assigned AC details, IR profile, remote policy, and pending command count.

| Field | Value |
|-------|-------|
| Endpoint name | Device Config |
| HTTP method | `GET` |
| URL | `/api/v1/device/config` |
| Authentication | Serial number (`X-Device-Serial`) |
| Required parameters | None |
| Optional parameters | None |

**Successful response (200):**

```json
{
  "data": {
    "device": {
      "uid": "ESP32-AC-LECTURE-01",
      "name": "ESP32-AC-LECTURE-01",
      "firmware_version": "1.2.0",
      "policy_version": 1
    },
    "ac": {
      "id": 9,
      "asset_code": "AC-LECTURE-01",
      "name": "AC - Lecture Hall 01",
      "model": "Samsung AR09TXECAWK",
      "location": "Lecture Hall 01, Ground Floor, Academic Block, Main Campus"
    },
    "ir_profile": {
      "version_id": 4,
      "version": 1,
      "validated": true,
      "temperature_min_c": 16,
      "temperature_max_c": 30,
      "temperature_step_c": 1.0,
      "capabilities": {
        "fan": true,
        "mode": true,
        "power": true,
        "swing": true,
        "setpoint": true
      },
      "protocol": null,
      "payloads": []
    },
    "remote_policy": {
      "effective": "disabled",
      "scope": "default",
      "version": 1,
      "notice": "This policy cannot physically prevent a handheld remote IR beam from reaching the AC."
    },
    "pending_commands": 0,
    "server_time": "2026-11-21T03:43:59+00:00"
  },
  "meta": {
    "request_id": "14fadec6-c4b6-441f-8cc4-74881f0cbf65"
  }
}
```

**When to call:** On startup and periodically to check configuration and pending command count.

---

### E.2 Pending Command Polling

**Purpose:** Retrieve commands pending execution for this device.

| Field | Value |
|-------|-------|
| Endpoint name | Pending Commands |
| HTTP method | `GET` |
| URL | `/api/v1/device/commands/pending` |
| Authentication | Serial number (`X-Device-Serial`) |
| Required parameters | None |
| Optional parameters | None |

**Successful response (200):**

```json
{
  "data": [
    {
      "id": "01a2078d-8819-717d-bc56-f5af04f55d0d",
      "command_type": "set_state",
      "desired_state": {
        "power": "on",
        "temperature_setpoint_c": 21
      },
      "issued_at": "2026-11-21T03:44:22+00:00",
      "expires_at": "2026-11-21T03:49:22+00:00",
      "correlation_id": "80eb0049-c44f-43af-bf4a-97b5d6d932fe",
      "profile_version_id": 4,
      "payloads": []
    }
  ],
  "meta": {
    "poll_again_seconds": 10,
    "delivery_note": "Acknowledgement and IR transmission result must be reported separately.",
    "request_id": "..."
  }
}
```

**When to call:** Every 5 seconds (per simulator polling interval). If no commands are returned, wait until the next interval.

---

### E.3 Command Acknowledgement

**Purpose:** Acknowledge receipt of a pending command.

| Field | Value |
|-------|-------|
| Endpoint name | Command Acknowledge |
| HTTP method | `POST` |
| URL | `/api/v1/device/commands/{command_id}/ack` |
| Authentication | Serial number (`X-Device-Serial`) |
| Required parameters | `event_id`, `occurred_at`, `result` |
| Optional parameters | `error_code`, `error_message` |

**Request body:**

```json
{
  "schema_version": 1,
  "event_id": "ack-ESP32-AC-LECTURE-01-01a2078d-1234567890",
  "occurred_at": "2026-11-21T03:44:23+00:00",
  "result": "accepted"
}
```

**result values:** `accepted`, `rejected`, `error`, `unsupported`

**Successful response (200):**

```json
{
  "data": {
    "accepted": true,
    "duplicate": false,
    "command": {
      "id": "01a2078d-8819-717d-bc56-f5af04f55d0d",
      "status": "received",
      "received_at": "2026-11-21T03:44:23+00:00",
      "transmission_at": null
    }
  },
  "meta": { "request_id": "..." }
}
```

**When to call:** Immediately after receiving a command via polling, before executing it.

---

### E.4 Command Transmission Result

**Purpose:** Report the IR transmission result for a command.

| Field | Value |
|-------|-------|
| Endpoint name | Transmission Result |
| HTTP method | `POST` |
| URL | `/api/v1/device/commands/{command_id}/transmission-result` |
| Authentication | Serial number (`X-Device-Serial`) |
| Required parameters | `event_id`, `occurred_at`, `result` |
| Optional parameters | `error_code`, `error_message`, `raw_timings` |

**Request body:**

```json
{
  "schema_version": 1,
  "event_id": "tx-ESP32-AC-LECTURE-01-01a2078d-1234567890",
  "occurred_at": "2026-11-21T03:44:23+00:00",
  "result": "transmitted"
}
```

**result values:** `transmitted`, `failed`, `error`

**Successful response (200):**

```json
{
  "data": {
    "accepted": true,
    "duplicate": false,
    "command": {
      "id": "01a2078d-8819-717d-bc56-f5af04f55d0d",
      "status": "completed",
      "received_at": "2026-11-21T03:44:23+00:00",
      "transmission_at": "2026-11-21T03:44:23+00:00"
    }
  },
  "meta": { "request_id": "..." },
  "notice": "IR transmission success does not prove physical AC execution."
}
```

**When to call:** After attempting IR transmission, once execution result is known.

> **Important:** Transmission success confirms the IR signal was sent, not that the physical AC executed the command. The backend marks the command as `completed` upon transmission, but the AC state is inferred, not physically confirmed.

---

### E.5 Device Heartbeat

**Purpose:** Report device connectivity and announce presence to the backend.

| Field | Value |
|-------|-------|
| Endpoint name | Device Heartbeat |
| HTTP method | `POST` |
| URL | `/api/v1/device/heartbeat` |
| Authentication | Serial number (`X-Device-Serial`) |
| Required parameters | `schema_version`, `event_id`, `occurred_at` |
| Optional parameters | `firmware_version`, `wifi_rssi_dbm`, `uptime_seconds` |

**Request body:**

```json
{
  "schema_version": 1,
  "event_id": "hb-ESP32-AC-LECTURE-01-1234567890",
  "occurred_at": "2026-11-21T03:43:59+00:00",
  "firmware_version": "1.2.0",
  "wifi_rssi_dbm": -45,
  "uptime_seconds": 3600
}
```

**Successful response (200):**

```json
{
  "data": {
    "accepted": true,
    "duplicate": false,
    "server_time": "2026-11-21T03:43:59+00:00",
    "policy": {
      "effective": "disabled",
      "version": 1
    }
  },
  "meta": { "request_id": "..." }
}
```

**When to call:** Every 5 minutes (300 seconds), and once on startup.

---

### E.6 Device State Reporting

**Purpose:** Report the current AC state (power, temperature setpoint, etc.) to the backend.

| Field | Value |
|-------|-------|
| Endpoint name | Device State Report |
| HTTP method | `POST` |
| URL | `/api/v1/device/state` |
| Authentication | Serial number (`X-Device-Serial`) |
| Required parameters | `schema_version`, `event_id`, `occurred_at`, `state` |
| Optional parameters | None |

**Request body:**

```json
{
  "schema_version": 1,
  "event_id": "state-ESP32-AC-LECTURE-01-1234567890",
  "occurred_at": "2026-11-21T03:44:23+00:00",
  "state": {
    "power": "on",
    "temperature_setpoint_c": 21,
    "operating_mode": "cool",
    "fan_setting": "auto",
    "swing_setting": "auto"
  }
}
```

**state.power values:** `"on"` or `"off"`

**state.temperature_setpoint_c:** Integer between 5 and 40

**Successful response (200):**

```json
{
  "data": {
    "accepted": true,
    "duplicate": false,
    "event_id": "01a1fc34-2b45-72ab-9a5a-5902220573e6"
  },
  "meta": { "request_id": "..." }
}
```

**When to call:** Immediately after executing a command, and as part of periodic state synchronization.

---

### E.7 Device Event Reporting (Generic)

**Purpose:** Send a generic device event (heartbeat, ack, transmission result, remote signal, state report, policy ack).

| Field | Value |
|-------|-------|
| Endpoint name | Device Event |
| HTTP method | `POST` |
| URL | `/api/v1/device/events` |
| Authentication | Serial number (`X-Device-Serial`) |
| Required parameters | `schema_version`, `message_type`, `event_id`, `occurred_at` |
| Optional parameters | `command_id`, `extra_payload` fields |

**message_type values:** `device.heartbeat`, `command.ack`, `command.transmission_result`, `remote.signal`, `state.report`, `policy.ack`

**Successful response (200/202):**

```json
{
  "data": {
    "accepted": true,
    "duplicate": false,
    "event_id": "...",
    "processing_status": "processed"
  },
  "meta": { "request_id": "..." }
}
```

**When to call:** When sending events that don't have a dedicated endpoint.

---

### E.8 Physical Remote Event Reporting

**Purpose:** Report detection of a physical infrared remote control signal.

| Field | Value |
|-------|-------|
| Endpoint name | Remote Events |
| HTTP method | `POST` |
| URL | `/api/v1/device/remote-events` |
| Authentication | Serial number (`X-Device-Serial`) |
| Required parameters | `event_id`, `occurred_at`, `classification`, `policy_result` |
| Optional parameters | `recognized_action`, `decoded_data`, `raw_timings` |

**classification values:** `recognized`, `unknown`, `decode_error`

**policy_result values:** `permitted`, `restricted`, `unknown`

**Request body:**

```json
{
  "schema_version": 1,
  "event_id": "remote-ESP32-AC-LECTURE-01-1234567890",
  "occurred_at": "2026-11-21T03:44:23+00:00",
  "classification": "recognized",
  "policy_result": "permitted",
  "recognized_action": "power_toggle",
  "decoded_data": { "command": "power_toggle" }
}
```

**Successful response (200):**

```json
{
  "data": {
    "accepted": true,
    "duplicate": false,
    "processing_status": "processed"
  },
  "meta": { "request_id": "..." },
  "notice": "Detected remote activity is informational; software cannot block the direct IR beam."
}
```

> **Notice:** Software policy cannot physically block a handheld remote's direct IR beam. Remote events are informational and affect synchronization/reporting behavior only.

**When to call:** When a physical remote signal is detected and decoded.

---

### E.9 Remote Policy Acknowledgement

**Purpose:** Acknowledge receipt of a remote control policy.

| Field | Value |
|-------|-------|
| Endpoint name | Policy Ack |
| HTTP method | `POST` |
| URL | `/api/v1/device/policy/ack` |
| Authentication | Serial number (`X-Device-Serial`) |
| Required parameters | `event_id`, `occurred_at`, `effective_policy`, `policy_version` |
| Optional parameters | None |

**effective_policy values:** `enabled`, `disabled`

**Request body:**

```json
{
  "schema_version": 1,
  "event_id": "policy-ESP32-AC-LECTURE-01-1234567890",
  "occurred_at": "2026-11-21T03:44:23+00:00",
  "effective_policy": "disabled",
  "policy_version": 1
}
```

**Successful response (200):**

```json
{
  "data": {
    "accepted": true,
    "duplicate": false,
    "policy_synced_at": "2026-11-21T03:44:23+00:00"
  },
  "meta": { "request_id": "..." }
}
```

**When to call:** After receiving a remote policy configuration from the config endpoint.

---

### E.10 Device Logout / Deactivation

**Status:** Not implemented. There is no device logout or deactivation endpoint. Device revocation is performed through the Manager Dashboard (backend-only operation).

---

## F. Command Lifecycle

The backend maintains a strict command lifecycle with distinct status values:

| Status | Meaning |
|--------|---------|
| `created` | Command was created by the Manager Dashboard |
| `published` | Command is queued and available for device polling |
| `received` | Device acknowledged receipt of the command |
| `transmission_attempted` | IR transmission was attempted (intermediate) |
| `completed` | Device completed IR transmission |
| `failed` | Command failed (rejected by device or transmission error) |
| `expired` | Command expired before completion |

### Lifecycle Flow

1. **Command Creation** — Manager issues a command via the Dashboard. Status: `created`.
2. **Publishing** — The backend publishes the command (sets status to `published`) so it becomes available via polling.
3. **Device Polling** — The simulator polls `/device/commands/pending` every 5 seconds.
4. **Device Receipt** — The simulator receives the command and sends an acknowledgement (`accepted`). Status: `received`.
5. **Command Execution** — The simulator executes the simulated action (e.g. sets AC power to ON).
6. **Transmission Reporting** — The simulator reports the transmission result. Status: `completed`.
7. **State Reporting** — The simulator reports the updated AC state.

### Delivery vs Execution

> **Command delivery is NOT the same as successful execution.**

- `received` means the device acknowledged receipt — the command has been delivered to the device.
- `completed` means the device reported successful IR transmission.
- Even `completed` does **not** mean the physical AC executed the command — IR transmission success only confirms the signal was sent.

The backend explicitly states: *"IR transmission success does not prove physical AC execution."*

---

## G. Device State Synchronization

### AC Power State

Tracked as one of: `known_on`, `known_off`, `unknown`, `stale`.

- `known_on` — AC is confirmed ON
- `known_off` — AC is confirmed OFF
- `unknown` — State has never been observed
- `stale` — Last observation is too old (configurable threshold, default 120 minutes)

### Temperature Setpoint

The desired temperature in degrees Celsius. This is the **setpoint**, not the room temperature. The setpoint does not imply the AC is currently at that temperature.

### Device Online/Offline Status

Determined by the device's `last_seen_at` timestamp:
- **Online:** Last seen within the offline threshold (default 5 minutes)
- **Offline:** Last seen older than the threshold
- **Unknown:** No heartbeat ever received

### Heartbeat

Sent every 5 minutes (300 seconds). Updates `last_seen_at` and marks the device as online.

### Timestamp Handling

All timestamps use ISO 8601 UTC format. The backend uses `occurred_at` (device-reported time) for event ordering and `server_received_at` (backend receipt time) for processing.

### How the Backend Determines Latest State

The `AcStateSnapshot` table stores the latest known state per AC unit. Out-of-order observations (older than the current snapshot) are ignored. State changes are recorded in `AcStateHistory` as an append-only audit trail.

---

## H. Complete Device Workflow

### H.1 Device Startup

1. Device powers on and reads its configured serial number.
2. Device sends an initial `POST /device/heartbeat` with its serial number.
3. Backend validates the serial number and marks the device online.
4. Device enters the continuous polling loop.

### H.2 Device Identity Validation

Every request includes the `X-Device-Serial` header. The backend looks up the device by serial number and verifies it is active. Invalid or missing serials receive a 401 response.

### H.3 Configuration Retrieval

Device calls `GET /device/config` to obtain:
- Assigned AC unit details
- IR profile (capabilities, temperature range, step size)
- Remote policy
- Pending command count

### H.4 Command Polling

1. Device calls `GET /device/commands/pending` every 5 seconds.
2. If commands are returned, the device processes each one.
3. If no commands, the device waits until the next interval.

### H.5 Power ON

1. Manager issues `power: "on"` command via Dashboard.
2. Device polls and receives the command.
3. Device sends acknowledgement (`accepted`).
4. Device sets internal `power_state = "on"`.
5. Device reports transmission result (`transmitted`).
6. Device reports state (`power: "on"`).

### H.6 Power OFF

Same as Power ON, but with `power: "off"` and `power_state = "off"`.

### H.7 Temperature Adjustment

1. Manager issues `temperature_setpoint_c: 24` command.
2. Device receives, acknowledges, executes (updates internal setpoint), reports transmission, and reports state.

### H.8 Command Acknowledgement

After receiving a command, the device calls `POST /device/commands/{id}/ack` with `result: "accepted"` before executing.

### H.9 Periodic Heartbeat

Every 5 minutes, the device calls `POST /device/heartbeat` to report connectivity and synchronize state.

### H.10 Network Disconnection and Recovery

- If a request fails (network timeout, 5xx), the device logs the error and retries on the next poll cycle.
- The simulator does not terminate on network failures.
- When the backend becomes available again, the next successful heartbeat restores online status.
- Commands issued while the device was offline remain pending (`published`) and are delivered when the device resumes polling.

---

## I. ESP32 Implementation Examples

### I.1 HTTP Request Construction (Arduino/C++)

```cpp
#include <WiFi.h>
#include <HTTPClient.h>
#include <ArduinoJson.h>

const char* ssid = "YOUR_WIFI_SSID";
const char* password = "YOUR_WIFI_PASSWORD";
const char* serialNumber = "HVAC-ESP32-2026-000001";
const char* baseUrl = "http://127.0.0.1:8000/api/v1";  // or https://hvac.cosmosalgos.com/api/v1

void sendHeartbeat() {
  if (WiFi.status() != WL_CONNECTED) return;

  HTTPClient http;
  http.begin(String(baseUrl) + "/device/heartbeat");
  http.addHeader("Content-Type", "application/json");
  http.addHeader("Accept", "application/json");
  http.addHeader("X-Device-Serial", serialNumber);

  DynamicJsonDocument doc(256);
  doc["schema_version"] = 1;
  doc["event_id"] = "hb-" + String(serialNumber) + "-" + String(millis());
  doc["occurred_at"] = getIso8601Utc();  // implement ISO 8601 UTC timestamp
  doc["firmware_version"] = "1.2.0";
  doc["wifi_rssi_dbm"] = WiFi.RSSI();
  doc["uptime_seconds"] = millis() / 1000;

  String body;
  serializeJson(doc, body);
  int httpCode = http.POST(body);

  if (httpCode == 200) {
    Serial.println("Heartbeat accepted");
  } else {
    Serial.println("Heartbeat failed: " + String(httpCode));
  }

  http.end();
}
```

### I.2 Command Polling Loop

```cpp
void pollCommands() {
  HTTPClient http;
  http.begin(String(baseUrl) + "/device/commands/pending");
  http.addHeader("Accept", "application/json");
  http.addHeader("X-Device-Serial", serialNumber);

  int httpCode = http.GET();
  if (httpCode == 200) {
    String payload = http.getString();
    DynamicJsonDocument doc(1024);
    deserializeJson(doc, payload);

    JsonArray commands = doc["data"];
    for (JsonObject cmd : commands) {
      const char* cmdId = cmd["id"];
      const char* cmdType = cmd["command_type"];
      JsonObject desiredState = cmd["desired_state"];

      acknowledgeCommand(cmdId);
      executeCommand(cmdType, desiredState);
      reportTransmissionResult(cmdId, "transmitted");
      reportDeviceState();
    }
  }
  http.end();
}
```

### I.3 Command Acknowledgement

```cpp
void acknowledgeCommand(const char* cmdId) {
  HTTPClient http;
  String url = String(baseUrl) + "/device/commands/" + cmdId + "/ack";
  http.begin(url);
  http.addHeader("Content-Type", "application/json");
  http.addHeader("X-Device-Serial", serialNumber);

  DynamicJsonDocument doc(256);
  doc["schema_version"] = 1;
  doc["event_id"] = "ack-" + String(serialNumber) + "-" + String(cmdId) + "-" + String(millis());
  doc["occurred_at"] = getIso8601Utc();
  doc["result"] = "accepted";

  String body;
  serializeJson(doc, body);
  http.POST(body);
  http.end();
}
```

### I.4 Polling Loop (Main)

```cpp
void loop() {
  static unsigned long lastPoll = 0;
  static unsigned long lastHeartbeat = 0;
  unsigned long now = millis();

  // Poll commands every 5 seconds
  if (now - lastPoll >= 5000) {
    pollCommands();
    lastPoll = now;
  }

  // Heartbeat every 5 minutes (300 seconds)
  if (now - lastHeartbeat >= 300000) {
    sendHeartbeat();
    lastHeartbeat = now;
  }

  delay(100);
}
```

> **Note:** Token-based authentication is **not** included in these examples. The `X-Device-Serial` header is the only authentication required.

---

## J. Error Handling and Retry Strategy

| Error Scenario | HTTP Status | Error Code | Recommended Action |
|----------------|-------------|------------|--------------------|
| Invalid serial number | 401 | `DEVICE_SERIAL_INVALID` | Verify serial number format; do not retry |
| Missing serial number | 401 | `DEVICE_SERIAL_REQUIRED` | Add `X-Device-Serial` header; do not retry |
| Unknown/inactive device | 401 | `DEVICE_NOT_FOUND` | Register device with backend; do not retry |
| Invalid request envelope | 422 | Validation error | Fix request format; do not retry |
| Command not found | 422 | Validation error | Use a valid command ID from polling |
| Command expired | 422 / processed | `expired_before_ack` | Command is no longer valid; discard |
| No pending commands | 200 | (empty data array) | Wait for next poll interval |
| Network timeout | (no response) | — | Retry with exponential backoff |
| Server error | 500 | — | Retry with exponential backoff |
| Duplicate request | 200 | `duplicate: true` | Event already processed; ignore |

### Retry Behavior

- **Network failures:** Retry on the next poll cycle (every 5 seconds). Implement exponential backoff for consecutive failures (e.g., 5s, 10s, 20s, up to a maximum).
- **Server errors (5xx):** Retry the request after a short delay.
- **Authentication failures (401):** Do not retry automatically — the serial number is invalid or missing.
- **Duplicate events:** The backend deduplicates by `event_id`. If a request is a duplicate, the response includes `"duplicate": true`.

### Device Reconnection

When network connectivity is restored:
1. Send a heartbeat to re-establish online status.
2. Resume command polling.
3. Any commands issued during the outage will be delivered on the next poll.

---

## K. Complete API Reference

| Endpoint | Method | Purpose | Serial Number Required |
|----------|--------|---------|------------------------|
| `/device/config` | GET | Get device config, AC details, IR profile, policy | Yes |
| `/device/commands/pending` | GET | Poll for pending commands | Yes |
| `/device/commands/{id}/ack` | POST | Acknowledge command receipt | Yes |
| `/device/commands/{id}/transmission-result` | POST | Report IR transmission result | Yes |
| `/device/heartbeat` | POST | Report device connectivity | Yes |
| `/device/state` | POST | Report AC state | Yes |
| `/device/events` | POST | Send generic device event | Yes |
| `/device/remote-events` | POST | Report physical remote signal | Yes |
| `/device/policy/ack` | POST | Acknowledge remote policy | Yes |

---

## L. Testing Examples

All examples use the GIFT University demo device serial `HVAC-ESP32-2026-000001`.

### L.1 Device Config (cURL)

```bash
# Success
curl -i http://127.0.0.1:8000/api/v1/device/config \
  -H "X-Device-Serial: HVAC-ESP32-2026-000001"

# Missing serial (error)
curl -i http://127.0.0.1:8000/api/v1/device/config

# Invalid serial (error)
curl -i http://127.0.0.1:8000/api/v1/device/config \
  -H "X-Device-Serial: INVALID-SERIAL"
```

### L.2 Pending Commands (cURL)

```bash
curl -i http://127.0.0.1:8000/api/v1/device/commands/pending \
  -H "X-Device-Serial: HVAC-ESP32-2026-000001"
```

### L.3 Command Acknowledgement (cURL)

```bash
curl -i -X POST http://127.0.0.1:8000/api/v1/device/commands/{command_id}/ack \
  -H "Content-Type: application/json" \
  -H "X-Device-Serial: HVAC-ESP32-2026-000001" \
  -d '{
    "schema_version": 1,
    "event_id": "ack-ESP32-AC-LECTURE-01-{command_id}-1234567890",
    "occurred_at": "2026-11-21T03:44:23+00:00",
    "result": "accepted"
  }'
```

### L.4 Device State Report (cURL)

```bash
curl -i -X POST http://127.0.0.1:8000/api/v1/device/state \
  -H "Content-Type: application/json" \
  -H "X-Device-Serial: HVAC-ESP32-2026-000001" \
  -d '{
    "schema_version": 1,
    "event_id": "state-ESP32-AC-LECTURE-01-1234567890",
    "occurred_at": "2026-11-21T03:44:23+00:00",
    "state": {
      "power": "on",
      "temperature_setpoint_c": 21,
      "operating_mode": "cool",
      "fan_setting": "auto",
      "swing_setting": "auto"
    }
  }'
```

### L.5 Device Heartbeat (cURL)

```bash
curl -i -X POST http://127.0.0.1:8000/api/v1/device/heartbeat \
  -H "Content-Type: application/json" \
  -H "X-Device-Serial: HVAC-ESP32-2026-000001" \
  -d '{
    "schema_version": 1,
    "event_id": "hb-ESP32-AC-LECTURE-01-1234567890",
    "occurred_at": "2026-11-21T03:43:59+00:00",
    "firmware_version": "1.2.0",
    "wifi_rssi_dbm": -45,
    "uptime_seconds": 3600
  }'
```

---

*End of documentation. This document describes the actual implemented API as verified through end-to-end testing.*
