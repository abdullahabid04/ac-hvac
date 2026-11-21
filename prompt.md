# Focused Implementation Tasks — GIFT University Demo, Device Identity, Command API & IoT Simulator

## 0. Overall Objective

You are working on the existing **Centralized IoT HVAC / AC Management Platform**.

I need you to implement the following four focused workstreams:

1. GIFT University branding on the existing login page.
2. GIFT University demo organization, admin account, hierarchy, AC inventory, and database seeders.
3. A consistent device serial-number generation and validation mechanism, along with improvements to the device command API response.
4. A reusable, continuously running Python IoT device simulator with a simple visual monitoring interface.

After all four workstreams are implemented, perform comprehensive testing of the existing web application, backend APIs, database integration, and Python simulator.

**This is a focused improvement task, not a redesign or rewrite of the platform.**

The existing application is already developed. Preserve its architecture, existing functionality, visual direction, API conventions, database relationships, and working components.

The most important instruction:

**Fix what is necessary. Do not break what is already working. Do not introduce unrelated changes.**

---

# 1. CONTEXT DISCOVERY — READ BEFORE IMPLEMENTING

You already have access to the existing project repository and its documentation.

Do not ask me to provide the project context again.

Start by discovering and inspecting the existing project.

## 1.1 Main Laravel application

Identify the current repository root and inspect:

- Laravel/PHP version.
- Frontend framework and build system.
- Authentication implementation.
- User, organization, membership, and role models.
- Database migrations and relationships.
- Existing factories and seeders.
- AC, location, and device models.
- Device registration and assignment mechanisms.
- Existing device API routes.
- Command creation, retrieval, acknowledgement, and reporting workflows.
- Existing API request/response structures.
- Authorization and tenant-isolation mechanisms.
- Existing automated tests.

Inspect actual code rather than assuming that the conceptual documentation describes the current implementation exactly.

## 1.2 Project documentation

Find and read the existing project documentation, especially:

1. Documentation index.
2. Project Proposal.
3. Software Requirements Specification.
4. Software Design Description.
5. Implementation Guide.
6. Backend Implementation Guide.
7. API Contract Guidelines.
8. Database Design Proposal.
9. Device and IoT Integration Guide.
10. Testing, Deployment and Operations Guide.
11. Variant 7 Theme Book and relevant UI specifications.

Also inspect the existing `project-reference` directory and any other relevant project-reference files.

Use the existing coding-agent context and these documents as the source of truth.

Priority:

1. Latest explicit client decisions.
2. Confirmed SRS requirements.
3. SDD and domain rules.
4. Approved UI specifications.
5. Engineering documentation.
6. Existing repository conventions.

Where documentation and implementation disagree, investigate and document the discrepancy. Do not blindly replace working implementation with a conceptual example.

## 1.3 Python simulator project

The existing Python testing project is expected at:

`D:\Projects\pythonProjects\ACHVAC_IoT_Testing`

Inspect this exact path.

If the directory does not exist, search the specified parent directory and the existing OpenCode workspace for the project or its earlier implementation.

I have already provided this path and the simulator requirements in the earlier project context.

There are two other Python projects in the parent directory. **Do not modify, rename, move, or interfere with either of them.**

The Python project must communicate with the existing Laravel application. It must not create a duplicate backend or a replacement Flask API.

If the simulator project does not exist, create it at the specified path.

---

# 2. TASK ONE — GIFT UNIVERSITY LOGIN PAGE BRANDING

## Objective

Customize only the existing login page to present a GIFT University-branded login experience.

Official website:

https://gift.edu.pk/

Official institution name:

**GIFT University**

Official tagline:

**Converting Knowledge into Practical Experience**

Location:

Gujranwala, Punjab, Pakistan.

## 2.1 Research the actual branding

Inspect the official GIFT University website.

Identify:

- Official logo and its available variants.
- Correct spelling and capitalization of the university name.
- Logo proportions and presentation.
- Brand colors used on the official website.
- Relevant typography and visual characteristics.
- How the logo and name are presented together.

Use the actual logo asset as a reference. Do not invent a logo or guess brand colors.

The official website is the reference, not a generic university theme.

If the official logo cannot be retrieved, investigate the available official assets and report the issue rather than substituting an unrelated logo.

The university's logo and name are protected marks. This implementation is for the requested demonstration; do not imply an official endorsement or partnership. Confirm that we have permission before any public commercial use.

## 2.2 Apply branding to the existing login page

Make a focused change to the existing login page.

The branding area above the email and password fields should include:

- GIFT University logo.
- GIFT University name.
- Optional official tagline, if it fits naturally.

The result should look like a professional university/facilities-management login page.

## 2.3 Strict scope

This is not a request to redesign the entire authentication system.

Do not:

- Rebuild the authentication module.
- Change login routes.
- Change authentication logic.
- Change password validation.
- Change session handling.
- Change user roles or permissions.
- Redesign the entire dashboard.
- Change other application pages.
- Replace the existing login form unnecessarily.

Preserve the existing email and password fields, validation, forgot-password behavior, remember-me functionality, login button, error messages, and responsive behavior.

Only make the necessary changes to the branding section and its immediate styling.

If the existing login page already has a good layout, preserve it.

**Acceptance criteria:**

- The correct GIFT University branding appears above the login fields.
- The actual logo is used where permitted and available.
- The login form still works exactly as before.
- Other application pages remain unchanged.

---

# 3. TASK TWO — GIFT UNIVERSITY DEMO ORGANIZATION AND SEEDERS

## Objective

Create a complete, reusable demonstration dataset for GIFT University.

I need to log in to the application using the GIFT University demo account and demonstrate the platform with realistic location and AC data.

The seeders must be usable again when deploying the application to another environment.

## 3.1 Organization

Create one demo organization:

**Organization Name:** GIFT University

**Location:** Gujranwala, Punjab, Pakistan

Use the existing organization model and schema.

Do not create a new tenant-management system.

## 3.2 Demo administrator account

Create one organization administrator.

Suggested account:

- Name: GIFT University Administrator
- Email: `demo.admin@gift.edu.pk`
- Role: Organization Administrator / Manager
- Organization: GIFT University

Use the existing user model, authentication system, password hashing, membership relationship, and role/permission implementation.

### Email verification

This is a temporary demonstration account.

I do not want to configure or send an actual email verification message for this seeded account.

Mark this specific seeded account as verified using the existing verification mechanism, for example by setting the appropriate verification timestamp.

Important:

- Do not globally disable email verification.
- Do not weaken verification for other users.
- Do not modify the application's authentication rules.
- Only the explicitly seeded GIFT demo administrator should be pre-verified.

### Password

Reuse the existing development/demo seeder password convention if one is already defined in the repository.

If no such convention exists, introduce a configurable `GIFT_DEMO_ADMIN_PASSWORD` environment setting.

Do not hardcode a production password into source code or expose credentials in logs or reports.

The seeder must hash the configured password using Laravel's existing hashing mechanism.

## 3.3 Location hierarchy

Create a realistic but small university demonstration hierarchy.

Use the existing hierarchy:

Organization → Site → Building → Floor → Spaces → ACs.

Suggested initial structure:

**Organization: GIFT University**

**Site:**
- Main Campus

**Buildings:**
- Academic Block
- Administration Block

**Floors:**
- Ground Floor
- First Floor

**Spaces:**
- Lecture Hall 01
- Computer Lab 01
- Faculty Office
- Conference Room

Use the existing generic typed-space model and relationships.

These are demonstration names, not claims about the university's actual internal building layout.

## 3.4 AC inventory

Create approximately four AC units.

Example:

| AC Name | Location |
|---|---|
| AC - Lecture Hall 01 | Lecture Hall 01 |
| AC - Computer Lab 01 | Computer Lab 01 |
| AC - Faculty Office | Faculty Office |
| AC - Conference Room | Conference Room |

Populate realistic metadata using existing schema fields:

- AC name.
- AC identifier.
- Manufacturer.
- Model.
- Supported temperature range.
- Current known setpoint, where appropriate.
- Power state.
- Device association.
- IR profile association, if supported.

Do not invent a validated IR profile.

If the repository has an existing demo/test profile, use it and clearly identify it as a simulation profile where appropriate.

## 3.5 IoT device assignment

Create one fully configured and functional demo IoT device.

It must be assigned to:

`AC - Lecture Hall 01`

This is the primary device for the demonstration.

The remaining two or three ACs can be included as inventory examples. They do not all need active simulator instances.

The primary AC must have:

- A unique device serial number.
- A valid device record.
- An active assignment.
- A valid organization/tenant relationship.
- Initial power state.
- Initial temperature setpoint.
- All required configuration for simulator communication.
- Correct references to its AC and organization.

The simulator must be able to communicate with the backend using this seeded device.

Do not create a device record that only looks functional in the UI but cannot retrieve and process commands.

## 3.6 Seeder architecture and safety

Create a dedicated seeder, for example:

`GiftUniversityDemoSeeder`

Use the actual project naming conventions.

The seeder must be:

- Idempotent.
- Safe to execute repeatedly.
- Relationship-aware.
- Compatible with existing migrations.
- Independent of manually generated database IDs.
- Safe to run in an already populated database.

Use `firstOrCreate`, `updateOrCreate`, stable identifiers, or an equivalent appropriate approach.

Do not:

- Truncate tables.
- Delete existing organizations.
- Reset the entire database.
- Drop tables.
- Overwrite unrelated users.
- Generate duplicate ACs or devices on repeated runs.

Register the seeder appropriately so that I can run the GIFT demo dataset directly during deployment.

Provide a clear command, such as:

`php artisan db:seed --class=GiftUniversityDemoSeeder`

If the existing `DatabaseSeeder` convention requires registration, follow the existing project design while keeping the demo seeder independently executable.

Do not introduce destructive production seeding behavior.

## 3.7 Seeder acceptance criteria

After executing the seeder:

1. GIFT University organization exists.
2. The demo administrator exists.
3. The account can log in without email verification.
4. The user belongs to the correct organization.
5. The organization hierarchy is visible.
6. Four ACs are visible in the correct spaces.
7. The primary AC has one active assigned device.
8. The assigned device has a valid unique serial number.
9. Re-running the seeder does not create duplicates.
10. Existing unrelated database records remain untouched.

---

# 4. TASK THREE — DEVICE SERIAL NUMBER GENERATION AND COMMAND API

This task is important because the ESP32 needs a stable identity and the simulator must behave like a real registered device.

Inspect the existing implementation first.

Do not assume that a device authentication or serial-number mechanism already exists.

## 4.1 Device serial number

Establish one consistent serial-number generation mechanism.

Suggested format:

`HVAC-ESP32-2026-000001`

Structure:

- `HVAC`: product/system identifier.
- `ESP32`: hardware family.
- `2026`: manufacturing/provisioning year.
- `000001`: unique sequential device number.

You may adapt this format if the existing schema or conventions provide a better compatible solution, but document the final format.

Requirements:

1. Unique across the platform.
2. Generated by the backend during device provisioning.
3. Immutable after creation.
4. Never reused after deletion or deactivation.
5. Enforced by a database unique constraint.
6. Safe under concurrent device registration.
7. Easy to copy into ESP32 firmware configuration.
8. Supported by the Python simulator.

Use an atomic database-backed counter or another concurrency-safe approach.

Do not generate serials using a simple row count or random number without collision protection.

The serial number should not depend on the organization name. It is the identity of the physical device, not the tenant.

## 4.2 Temporary serial-number-based device validation

For the current demonstration, I do not want to introduce a full token/certificate provisioning workflow.

I want the device to identify itself using its preconfigured serial number.

The intended workflow:

1. Device serial is generated and registered in the backend.
2. The serial is stored in the ESP32 firmware configuration.
3. The device sends its serial number when communicating with the backend.
4. The backend checks whether the serial exists.
5. The backend checks whether the device is active.
6. The backend checks its current AC assignment.
7. The backend permits only the authorized device to access its assigned device operations.

Unknown, inactive, duplicate, or unassigned device identities must be rejected appropriately.

### Important security distinction

A serial number is an identifier, not a cryptographic authentication credential.

Serial-only validation is a temporary demonstration mechanism and must not be described as production-grade device authentication.

For this limited demonstration:

- Keep the implementation clearly isolated as a temporary/demo authentication mode.
- Do not pretend that serial-only identification prevents spoofing.
- Enforce device registration, active status, current assignment, tenant boundaries, input validation, rate limiting, and command ownership.
- Use HTTPS/TLS when available.
- Keep a clean extension point for future per-device credentials, tokens, or certificates.
- Do not introduce a complex token provisioning workflow now.

Do not expose commands for another device by changing the serial number in a request.

## 4.3 Device API inspection

Before modifying the API, inspect the actual:

- Device registration routes.
- Device polling routes.
- Command retrieval service.
- Command acknowledgement route.
- Transmission-result reporting route.
- State-reporting route.
- Device heartbeat route.
- Existing command database model.
- Current frontend command creation workflow.

The existing API contract documentation is conceptual. Actual repository implementation takes precedence where it is already approved and working.

Preserve existing API compatibility wherever possible.

## 4.4 Command response: support multiple representations

I want to evaluate three different ways of representing a command for the ESP32.

The backend should provide equivalent representations of the same requested AC change so the device can use the interpretation strategy it supports.

### Approach A — Direct command

Return an explicit action.

Examples:

- `power_on`
- `power_off`
- `set_temperature`

Example conceptual representation:

```json
{
  "command_type": "direct_action",
  "action": "set_temperature",
  "value": 26
}
```

For power:

```json
{
  "command_type": "direct_action",
  "action": "power_on"
}
```

### Approach B — Desired state

Return the complete target state.

Example:

```json
{
  "command_type": "desired_state",
  "desired_state": {
    "power": "on",
    "temperature_setpoint_c": 26
  }
}
```

The device interprets this as the state it should reach.

### Approach C — Current state and target state

Return the current known state and the desired new state.

Example:

```json
{
  "command_type": "state_transition",
  "current_state": {
    "power": "on",
    "temperature_setpoint_c": 25
  },
  "target_state": {
    "power": "on",
    "temperature_setpoint_c": 26
  }
}
```

The device can compare the two states and determine what needs to change.

Include a state version or timestamp where supported. The current state can become stale, so the device must not blindly apply a transition based on an outdated state.

## 4.5 Device-side strategy selection

The intention is to let the device support different command interpretation strategies.

Do not make the backend send contradictory commands or cause the device to execute the same operation multiple times.

If all three representations are returned together, they must be clearly grouped as equivalent alternatives belonging to the same command ID.

The device should select one strategy through a configurable device setting, with a deterministic default.

For example:

- `direct_action`
- `desired_state`
- `state_transition`

The simulator must support switching between these strategies for testing.

Do not let the simulator execute all three representations.

Only one representation of a command may be executed.

If the existing API architecture makes a single versioned envelope with equivalent representations impractical, propose the smallest backward-compatible implementation and document the reason.

## 4.6 Command lifecycle must remain intact

Preserve the existing command lifecycle.

The following events are different:

1. Command created by the manager.
2. Command queued or made available.
3. Device receives the command.
4. Device acknowledges receipt.
5. Device processes the command.
6. Device reports its execution/transmission result.
7. Device reports its updated known state.
8. Backend stores the event and updates its state projection.
9. Portal displays the latest reported state.

Do not treat command creation as successful execution.

Do not treat device acknowledgement as proof of physical AC execution.

An IR transmission report is not proof that the AC physically changed state.

The simulator must preserve these distinctions.

## 4.7 Temperature semantics

The platform's temperature value is the AC target/setpoint, not room temperature.

Use the existing validated temperature range and one-degree increments.

When constructing full-state IR commands, preserve any other state fields required by the validated AC profile.

Do not invent support for unvalidated AC models or IR protocols.

---

# 5. TASK FOUR — PYTHON IoT DEVICE SIMULATOR

This is the most important demonstration/testing component.

The Python application must behave like a continuously running ESP32 device connected to an AC.

It must communicate with the actual Laravel backend.

It must not be a mock backend or an independent replacement API.

## 5.1 Project location

Use:

`D:\Projects\pythonProjects\ACHVAC_IoT_Testing`

Inspect any existing files first.

Preserve existing useful work and conventions.

Do not interfere with the two neighboring Python projects.

## 5.2 Simulator architecture

Create a reusable simulator that supports one or multiple logical IoT devices.

Each simulated device should have:

- Serial number.
- Assigned AC ID.
- Configurable API connection.
- Device active/inactive status.
- Current simulated power state.
- Current temperature setpoint.
- Last successful poll.
- Last heartbeat/state report.
- Last processed command.
- Command interpretation strategy.
- Device connectivity status.
- Command history.

Use the real backend's actual API routes and payloads.

Do not invent endpoints based on conceptual documentation.

If the current backend lacks a required device endpoint, implement the smallest necessary compatible addition in the Laravel application before connecting the simulator.

## 5.3 Continuous operation

The simulator must run continuously.

One execution should start the simulator and keep it running until the user stops it.

It must not terminate after:

- One polling request.
- One command.
- An empty response.
- A temporary network failure.
- A timeout.
- A recoverable server error.

Implement a robust event loop with:

- Graceful shutdown.
- Configurable polling interval.
- Retry and reconnect handling.
- Bounded exponential backoff for repeated failures.
- Proper timeout handling.
- Clear logging.
- No uncontrolled busy loops.
- No duplicate command execution.

Default testing polling interval:

**5 seconds.**

This is the interval for checking the backend for pending commands.

## 5.4 Periodic state and heartbeat updates

In addition to command polling, periodically report device status.

Default periodic interval:

**5 minutes.**

Report:

- Device serial number.
- Device connectivity/heartbeat.
- Current known AC power state.
- Current setpoint when AC is ON.
- Device timestamp.
- Last successful command information where appropriate.

When AC power is OFF, do not invent a new current operating temperature. Preserve the last known setpoint as appropriate, but do not report it as a measured room temperature.

After a command is successfully simulated, report the updated state immediately rather than waiting for the next five-minute interval.

The five-second polling and five-minute periodic reporting must be independently configurable.

## 5.5 Simulated AC behavior

The simulator must maintain a real internal state for each simulated AC.

### Power ON

When the device receives a valid ON command:

1. Log command receipt.
2. Validate the command.
3. Acknowledge receipt.
4. Change the simulated AC power state to ON.
5. Preserve or apply the appropriate target setpoint.
6. Report the simulated execution/transmission outcome.
7. Report updated known state.
8. Log the final result.

### Power OFF

When the device receives a valid OFF command:

1. Log command receipt.
2. Validate the command.
3. Acknowledge receipt.
4. Change simulated power state to OFF.
5. Preserve the last known setpoint where appropriate.
6. Report the execution/transmission outcome.
7. Report updated known state.
8. Log the final result.

### Temperature adjustment

When the device receives a temperature change:

1. Log command receipt.
2. Acknowledge receipt.
3. Check the supported range and step.
4. Apply the selected command interpretation strategy.
5. Update the simulated setpoint.
6. Report the result.
7. Report updated known state.

A temperature adjustment while OFF must follow the existing AC profile and backend rules. Do not silently invent a behavior that conflicts with the actual application.

### State synchronization

The simulator must keep its internal state consistent with the last successfully processed command and the backend's reporting workflow.

Handle duplicate commands idempotently.

Do not execute the same command repeatedly when a poll returns a previously processed command.

---

# 6. SIMULATOR VISUAL MONITORING INTERFACE

I need to visually observe what the simulator is doing during the demonstration.

Terminal logs alone are not sufficient.

Create a small, clean, easy-to-use local monitoring interface for the Python simulator.

**Do not modify the existing Laravel web application's frontend to create this monitor.**

## 6.1 Preferred interface

Use a lightweight desktop dashboard, preferably Python Tkinter if it fits the existing project.

Avoid unnecessary large dependencies.

The dashboard should have a professional, clean layout.

### Device overview

Show each simulated device in an individual card or row.

Display:

- Device serial number.
- Associated AC name.
- Connectivity status.
- Power state: ON/OFF.
- Current setpoint.
- Last poll time.
- Last heartbeat/state update.
- Last command.
- Last command result.

Use clear visual indicators:

- Green: ON / successful.
- Gray: OFF / idle.
- Amber: pending / warning / stale.
- Red: failed / disconnected.

Do not confuse device connectivity with AC power state.

### Live event log

Provide a continuously updating log panel.

Show events such as:

```text
[10:00:00] DEVICE CONNECTED
[10:00:05] POLL REQUEST SENT
[10:00:05] NO PENDING COMMANDS

[10:02:10] COMMAND RECEIVED
Device: HVAC-ESP32-2026-000001
Command: POWER ON

[10:02:10] COMMAND ACKNOWLEDGED
[10:02:10] SIMULATED AC STATE: ON
[10:02:10] STATE REPORT SENT
[10:02:11] BACKEND REPORT ACCEPTED
```

Use actual timestamps and actual responses, not fabricated success messages.

### Controls

Provide simple controls:

- Start simulator.
- Stop simulator.
- Pause/resume polling if safe.
- Add a simulated device.
- Enable/disable a device.
- Select command interpretation strategy.
- View selected device details.
- Clear the visible log panel without deleting persistent history.

If practical, allow a user to run a manual test against a selected simulated device.

The monitor must remain responsive while simulator requests execute in the background.

Use thread-safe communication between simulator workers and the GUI.

## 6.2 Persistent logs

Store structured logs locally.

Include:

- Timestamp.
- Device serial.
- AC identifier.
- Event type.
- Command ID where relevant.
- API endpoint/action where useful.
- Result.
- Error details with secrets removed.

Use log rotation or bounded retention.

The monitoring GUI should be able to show recent activity.

## 6.3 Easy manual operation

I should be able to use this simulator again in the future without editing Python source code.

Provide:

- Device registration through the GUI or CLI.
- Configuration persistence.
- Start/stop.
- Add/edit/disable device.
- Change polling interval.
- Change command interpretation strategy.
- Inspect simulated state.
- Inspect command history.
- Run individual test scenarios.

SQLite may be used for the simulator's local device registry and test history.

Keep this SQLite database separate from the Laravel application's database.

Provide a simple startup command and a short README.

The normal startup should make it obvious that the simulator is running and that its events are visible in the dashboard.

## 6.4 Important limitation

This is software simulation.

It verifies backend communication and simulated device behavior. It does not physically transmit infrared signals to an AC.

Do not claim physical ESP32 or IR hardware validation from simulator results.

---

# 7. TASK EXECUTION ORDER

Implement in this exact order.

### Phase 1 — Repository inspection and safety plan

- Inspect the application.
- Inspect existing Python project.
- Read relevant documentation.
- Map existing database and API relationships.
- Identify files that need modification.
- Identify possible regressions.
- Prepare a concise implementation checklist.

Do not begin with a broad refactor.

### Phase 2 — Login page branding

- Research official GIFT University branding.
- Apply the branding to the existing login page only.
- Verify login still works.

### Phase 3 — GIFT demo seeders

- Implement the demo organization.
- Implement the admin account.
- Implement location hierarchy.
- Implement four AC records.
- Implement the primary assigned device.
- Verify idempotency.

### Phase 4 — Device serial number mechanism

- Inspect existing device identity.
- Implement the agreed serial-number generation mechanism.
- Implement temporary serial-based device validation.
- Preserve a clear upgrade path to stronger authentication.

### Phase 5 — Device command API improvements

- Inspect existing device API contract.
- Implement compatible command representations.
- Ensure a device selects and executes only one interpretation.
- Preserve existing acknowledgement and state-reporting behavior.

### Phase 6 — Python simulator

- Locate or create the simulator project.
- Implement the API client.
- Implement continuous polling.
- Implement simulated AC state.
- Implement command processing.
- Implement acknowledgement and reporting.
- Implement five-minute periodic status reporting.
- Implement live visual monitoring.
- Implement persistent logs and device configuration.

### Phase 7 — Comprehensive testing (LAST)

Do not perform the full end-to-end testing phase before all implementation work is complete.

After the four workstreams are implemented, run the tests described below.

Fix defects and repeat the relevant tests.

---

# 8. FINAL TESTING AND ACCEPTANCE CRITERIA

Testing is the final phase.

The purpose is to verify the complete real workflow using the GIFT University demo account and its primary assigned AC/device.

## 8.1 Login and organization tests

- GIFT demo administrator can log in.
- Email verification does not block this seeded account.
- User is assigned to the correct organization.
- Organization data is tenant-scoped.
- Other organizations and users remain unaffected.

## 8.2 Seeder tests

- Run the GIFT seeder once.
- Verify all expected records.
- Run it a second time.
- Confirm no duplicate records.
- Confirm no unrelated records are deleted or overwritten.

## 8.3 Serial-number tests

- Generate a new serial.
- Confirm format.
- Confirm uniqueness.
- Confirm duplicate serial rejection.
- Confirm inactive device rejection.
- Confirm unknown device rejection.
- Confirm correct device-to-AC assignment.
- Confirm another device cannot access the primary device's commands by changing its submitted serial.

## 8.4 Command API tests

Test the three command representations:

1. Direct command.
2. Desired state.
3. Current state and target state.

Verify:

- Correct command ID.
- Correct device assignment.
- Correct AC assignment.
- One interpretation executed per command.
- No duplicate execution.
- Invalid commands rejected.
- Expired commands rejected.
- Acknowledgement correctly recorded.
- Execution/transmission result correctly recorded.
- Updated state correctly recorded.

Test stale current-state information in the state-transition approach.

## 8.5 Python simulator tests

- Simulator starts successfully.
- It remains running continuously.
- Command polling occurs every five seconds by default.
- Periodic state/heartbeat reporting occurs every five minutes.
- Network failures do not terminate the simulator.
- Reconnection works.
- Empty polls do not create errors.
- Duplicate commands are not executed twice.
- Power ON works.
- Power OFF works.
- Temperature changes work.
- All three command interpretation strategies work.
- State is correctly reported.
- Simulator GUI remains responsive.
- Logs accurately show actual events.
- Simulator can be stopped gracefully.

## 8.6 Actual web-to-simulator end-to-end tests

This is the most important test.

Use the actual GIFT University demo account in the existing web application.

Use the seeded primary AC and its assigned simulator device.

### Scenario A — Power ON

1. Log in to the web application as the GIFT demo administrator.
2. Open the GIFT University organization.
3. Navigate to the primary AC.
4. Click ON.
5. Verify that the backend creates a command.
6. Verify that the simulator receives it.
7. Verify acknowledgement.
8. Verify simulated AC state changes to ON.
9. Verify updated state is reported to the backend.
10. Verify the web application displays the updated reported state.

### Scenario B — Temperature increase

1. Increase the setpoint from the web application.
2. Verify the backend command.
3. Verify simulator receipt.
4. Verify the simulated setpoint changes.
5. Verify the execution result.
6. Verify the updated setpoint appears in the web application.

### Scenario C — Temperature decrease

Repeat the workflow with a lower setpoint.

### Scenario D — Power OFF

1. Click OFF in the web application.
2. Verify command delivery.
3. Verify acknowledgement.
4. Verify simulated state changes to OFF.
5. Verify backend state report.
6. Verify web application reflects the updated reported state.

### Scenario E — Continuous operation

Leave the simulator running.

Verify:

- Polling continues.
- Heartbeat/state reporting continues.
- No duplicate commands execute.
- Logs remain visible.
- Temporary connectivity failures recover.

Record actual timestamps and command lifecycle latency.

A five-second polling interval does not guarantee that every complete workflow finishes in five seconds.

## 8.7 Regression testing

Check that unrelated existing features continue working.

At minimum:

- Login/logout.
- Organization isolation.
- Locations hierarchy.
- AC inventory.
- AC details.
- Power controls.
- Temperature controls.
- Existing command history.
- Device reporting.
- Existing frontend navigation.

Do not redesign the Grid View or other already approved UI areas as part of this task.

## 8.8 Testing reports

Provide a final report containing:

- Test case.
- Expected result.
- Actual result.
- Pass/fail status.
- Relevant evidence.
- Failed cases.
- Outstanding limitations.
- Any tests not executed.

Do not report a test as passed unless it was actually executed.

---

# 9. STRICT CHANGE AND SAFETY RULES

These rules apply throughout the implementation.

1. Do not rewrite the application.
2. Do not change the overall frontend design.
3. Do not redesign the dashboard.
4. Do not modify unrelated pages.
5. Do not change the existing authentication architecture.
6. Do not introduce unnecessary dependencies.
7. Do not create a second backend for the simulator.
8. Do not invent API routes without inspecting the current implementation.
9. Do not modify neighboring Python projects.
10. Do not delete existing database records.
11. Do not run destructive migrations or database resets.
12. Do not change production configuration or deployment settings.
13. Do not expose passwords, tokens, or other secrets in logs.
14. Preserve existing tenant isolation.
15. Preserve command lifecycle and state provenance.
16. Do not treat device connectivity as AC power state.
17. Do not treat IR transmission as physical AC confirmation.
18. Do not claim the simulator proves physical hardware functionality.

Before modifying an existing component or service, inspect its current dependencies and usage.

Before changing a database schema, identify the migration impact and confirm that existing records remain compatible.

If an implementation requires a potentially destructive change, stop and explain the risk rather than proceeding blindly.

Keep all changes focused, maintainable, and consistent with the existing application.

---

# 10. FINAL DELIVERY REPORT

When finished, provide a structured summary.

### A. Login branding
- Official logo asset used.
- Relevant files changed.
- What changed on the login page.
- Confirmation that authentication remains intact.

### B. GIFT University seeders
- Seeder names.
- Created organization.
- Created admin account identifier.
- Created hierarchy.
- Number of ACs.
- Primary device serial number.
- Exact command to execute the seeder.
- Idempotency verification.

Do not print the configured password in the report.

### C. Device identity and API
- Final serial-number format.
- Serial generation mechanism.
- Validation behavior.
- Temporary security limitations.
- Supported command representations.
- Actual API endpoints modified.
- Backward-compatibility considerations.

### D. Python simulator
- Project location.
- Main files/modules.
- Startup instructions.
- Default polling interval.
- Periodic reporting interval.
- GUI monitoring instructions.
- Log location.
- How to add another device.
- How to switch command interpretation strategies.
- How to stop the simulator.

### E. Testing
- Test results.
- End-to-end test evidence.
- Failed or skipped cases.
- Remaining limitations.
- Any recommended follow-up work.

### F. Changed files
Provide a clear list of changed, created, and removed files.

Do not hide failures or claim completion without evidence.

---

# FINAL INSTRUCTION

I have a university demonstration coming up shortly.

I need a stable, working result, not a large collection of unfinished changes.

Focus only on the four requested workstreams.

The most important demonstration workflow is:

**GIFT University Login → GIFT Organization → Primary AC → Web ON/OFF or Temperature Change → Python Simulator Receives Command → Simulated State Changes → Backend Receives Updated State → Web Application Displays Updated State.**

I want to watch this entire workflow through the existing web application and the simulator's visual monitoring interface.

Keep the implementation small, safe, and functional.

Do not sacrifice existing working features to implement new ones.

**We are fixing and completing the existing product, not breaking it and rebuilding it.**
