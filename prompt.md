# MASTER IMPLEMENTATION PROMPT
## AC HVAC IoT Device Simulator & End-to-End Testing Platform

### 1. Project Objective

We need to develop a separate, mature, reusable Python-based IoT Device Simulation and End-to-End Testing Platform for our existing Centralized AC Management System. The primary objective is to thoroughly test the complete communication and command lifecycle between the existing web portal, its backend APIs, the database, and simulated IoT devices. This must be a real end-to-end implementation, not merely a collection of isolated API tests or mocked responses.

The system must simulate how an actual ESP32-based IoT device installed with an air conditioner communicates with our AC Management backend. We must be able to operate an AC from the web portal, have the backend save the requested action, let the simulated IoT device periodically poll the backend, retrieve pending commands, execute the simulated action, report command acknowledgement and updated AC state, and finally verify that the backend and web portal reflect the device-reported state correctly. We need to test this complete cycle for every supported operation, including power ON/OFF and temperature adjustments, along with all relevant API endpoints, device authentication, command handling, state reporting, error handling, and edge cases.

The final deliverable must include two capabilities:

1. **Automated Testing:** You must implement and execute comprehensive automated test scenarios covering the complete system workflow.
2. **Reusable Manual Testing Environment:** You must deliver a mature, configurable Python testing application that I can continue using independently in the future. When I add a new AC or IoT device through the web portal, I should be able to register that device in this Python application and use it for further manual or automated testing without rewriting scripts.

### 2. Project Location and Directory Structure

Create the new Python project at the following location:

`D:\Projects\pythonProjects\ACHVAC_IoT_Testing`

Directory naming is important:
- Parent directory: `Projects`
- Python projects directory: `pythonProjects` (lowercase `p` in Python and uppercase `P` in Projects, with no space).
- New project directory: `ACHVAC_IoT_Testing`.

Before creating anything, inspect the specified directory and verify the existing folder structure. If the parent directories do not exist, create them. There are already two other Python projects in this location. **Do not modify, move, delete, rename, or interfere with those projects.** Keep this project completely isolated with its own virtual environment, dependencies, configuration, database, logs, test reports, and documentation.

Use a clean, maintainable Python project structure. Organize the application into separate modules for device simulation, API communication, command processing, device registration, test execution, test scenarios, configuration, logging, reporting, and utilities. Use a `requirements.txt` or another appropriate dependency-management approach, provide `.env.example`, and include a comprehensive README with setup and execution instructions.

### 3. First Step: Inspect the Existing AC Management System

Before writing code, thoroughly inspect the existing AC Management Portal repository and its documentation. Understand the actual implementation rather than making assumptions about how the system works.

Inspect:

- Backend framework, application structure, and API routing.
- Database schema and AC/device relationships.
- AC registration and device assignment workflows.
- Device serial number and identification mechanisms.
- Existing authentication and authorization implementation.
- API endpoints used by IoT devices.
- Command creation and persistence.
- Pending command retrieval and polling mechanisms.
- Command acknowledgement and execution reporting.
- AC power-state reporting.
- Temperature setpoint updates and validation.
- Device connectivity and last-seen tracking.
- Command status lifecycle and error handling.
- Existing event logs, activity records, and audit mechanisms.
- Frontend status updates and how the portal obtains the latest device-reported state.

Read the existing API documentation and inspect the actual controllers, routes, services, models, migrations, and relevant frontend components. Do not invent API endpoints, payload structures, database fields, status values, or authentication mechanisms.

**The existing AC Management backend is the system under test and the source of truth.** The Python project must integrate with its real APIs and actual data structures. If documentation and implementation disagree, identify the discrepancy and use the verified implementation as the basis, documenting the difference.

Do not create a duplicate backend, duplicate database, or replacement API for the AC Management application. This Python project is a testing and IoT simulation client, not another AC Management backend. In particular, do not create an unnecessary Flask API: communicate with the existing AC Management API using Python HTTP clients.

### 4. Define Test Sets Before Implementation

Before implementing the simulator, prepare a comprehensive test strategy and test-case inventory. Identify every relevant API, device operation, state transition, and failure scenario.

Create a dedicated `test-plan/` directory containing:

- Test Strategy
- Test Case Inventory
- API Test Matrix
- End-to-End Workflow Scenarios
- Device Simulation Scenarios
- Error and Recovery Scenarios
- Expected Results and Acceptance Criteria

Assign unique IDs to test cases, such as `TC-DEVICE-001`, `TC-CMD-001`, and `TC-E2E-001`.

Organize tests into logical test sets, including at least the following:

**A. Device Registration and Identification**
- Register a new simulated IoT device.
- Associate a device with the correct AC.
- Verify device serial number handling.
- Verify device-to-AC relationships.
- Test valid and invalid device identifiers.
- Test duplicate serial numbers.
- Test unregistered devices.
- Test missing or invalid credentials.
- Verify that one device cannot retrieve another device's commands or report state for another AC.

**B. Device Authentication and API Security**
- Valid device authentication.
- Invalid credentials.
- Expired or revoked credentials, if supported.
- Missing authentication headers.
- Invalid serial number/token combinations.
- Unauthorized command retrieval.
- Unauthorized state reporting.
- Tenant and device isolation.
- Verify that authentication failures do not modify AC state.

Use the authentication mechanism already implemented by the backend. Do not assume that a particular token format is available.

**C. Device Polling and Command Retrieval**
- Device polls when no pending command exists.
- Device polls when one command is pending.
- Device polls when multiple commands are pending.
- Device receives only commands assigned to itself.
- Device receives commands in the correct order.
- Device handles repeated polling.
- Device handles delayed responses.
- Device handles empty responses.
- Device handles malformed responses.
- Device handles API timeouts and server errors.
- Device reconnects and resumes polling after temporary failure.

**D. Power ON/OFF Command Tests**
- Turn an AC ON through the web portal.
- Verify that the command is saved in the backend.
- Verify that the device retrieves it during polling.
- Verify that the simulator executes the ON command.
- Verify that the simulator acknowledges command receipt.
- Verify that the simulator reports the updated ON state.
- Verify that the backend records the acknowledgement and reported state.
- Verify that the portal displays the updated state.

Repeat the complete workflow for OFF.

Also test:
- ON when the AC is already ON.
- OFF when the AC is already OFF.
- Rapid ON/OFF requests.
- Multiple consecutive state changes.
- Repeated command delivery.
- Duplicate acknowledgements.
- Delayed acknowledgements.
- Failed command execution.
- Device disconnecting after receiving a command.
- Device reconnecting and recovering the correct command state.

**E. Temperature Setpoint Tests**
- Increase temperature by one degree.
- Decrease temperature by one degree.
- Set a new temperature from the portal.
- Verify the backend command payload.
- Verify device command retrieval.
- Verify simulated execution.
- Verify acknowledgement and reported temperature.
- Verify the updated setpoint in the portal.

Test minimum and maximum supported temperatures based on the actual AC profile and backend validation. Also test invalid temperatures, repeated adjustments, boundary conditions, unavailable setpoints, and concurrent temperature changes.

Remember: the setpoint represents the AC target temperature, not measured room temperature.

**F. Command Lifecycle and Reliability**
Verify every command lifecycle stage supported by the actual implementation:

- Requested by the web application.
- Persisted by the backend.
- Available for device retrieval.
- Retrieved by the device.
- Acknowledged by the device.
- Simulated execution completed.
- Execution result reported.
- Backend state updated.
- Frontend reflects the latest reported state.

Test duplicate requests, retries, idempotency, delayed responses, failed execution, command expiration if supported, and recovery after disconnection.

Never treat a command being accepted or queued as proof that the device executed it. Likewise, never treat successful simulated execution as proof of physical AC execution.

**G. Device Connectivity and State Reporting**
- Device startup and initial registration.
- Initial status reporting.
- Periodic heartbeat or polling.
- Online/offline transitions.
- Last-seen timestamps.
- Delayed status updates.
- Stale device state.
- Device disconnection.
- Device reconnection.
- Incorrect or missing timestamps.
- Device reporting a state that differs from the requested command.
- Unknown or unavailable AC state.

Device connectivity and AC power state must remain separate concepts. An online device does not necessarily mean its AC is ON.

**H. Multi-Device and Multi-Location Testing**
- Multiple simulated devices polling simultaneously.
- Multiple ACs receiving different commands.
- Different manufacturers and AC profiles where supported.
- Multiple buildings, floors, and spaces.
- Different organizations or tenants.
- Concurrent commands for different devices.
- Verify that commands and reported states never cross device or organization boundaries.

**I. Error Handling and Recovery**
- Backend unavailable.
- Network timeout.
- HTTP 4xx and 5xx responses.
- Invalid JSON responses.
- Unexpected API payloads.
- Database or persistence failures.
- Device process interruption.
- Duplicate events.
- Failed acknowledgement.
- Retry exhaustion.
- Reconnection and recovery.

Record the expected result for every scenario and distinguish expected failures from actual defects.

### 5. Build the Python IoT Device Simulator

After preparing the test strategy, implement the Python application.

The central component must be an IoT device simulator that behaves like an ESP32 device communicating with the existing backend.

Each simulated device must have its own identity and runtime state, including:

- Device serial number.
- Associated AC identifier.
- Organization/tenant association where applicable.
- Authentication credentials or token reference.
- Device online/offline state.
- Last polling timestamp.
- Last successful communication timestamp.
- Current simulated AC power state.
- Current simulated temperature setpoint.
- Last retrieved command.
- Last acknowledged command.
- Last execution result.

The actual fields and identifiers must match the existing backend's data model.

Implement a configurable polling mechanism. For automated end-to-end testing, set the default polling interval to **5 seconds**. The polling interval must be configurable without changing application code so that we can later use realistic intervals such as one minute or two minutes.

Each simulated device must independently:

1. Initialize using its registered identity and credentials.
2. Connect to the existing AC Management API.
3. Poll for pending commands every five seconds during testing.
4. Send its actual serial number and the required authentication credentials.
5. Retrieve any pending commands assigned to it.
6. Validate the command and its parameters.
7. Execute the command in its simulated internal state.
8. Report command receipt/acknowledgement using the actual backend contract.
9. Report the simulated execution result and updated AC state.
10. Update its local state only according to the defined execution lifecycle.
11. Continue polling for subsequent commands.

If no new command exists, it must continue normal polling without producing unnecessary errors.

Implement proper exception handling, timeouts, retries, and graceful shutdown. One device failing must not crash the entire simulator or prevent other simulated devices from running.

### 6. Simulate Actual Device Behaviour

The simulator must behave like an actual IoT device, not simply echo backend responses.

For example:

- If the backend sends an ON command, the simulated device must change its internal power state to ON.
- If the backend sends an OFF command, it must change its internal power state to OFF.
- If the backend sends a new temperature setpoint, it must validate and apply that setpoint.
- It must then report the resulting state back to the backend.
- The simulator must report realistic success or failure results based on the execution scenario.

Maintain a local simulated device state so that the simulator can detect differences between its previous state and the newly applied command.

Support configurable failure injection for testing, such as:
- Command received but execution fails.
- Command received but acknowledgement is delayed.
- Device becomes unavailable.
- Network request times out.
- Device reports an unexpected state.
- Device process restarts during a pending command.

These features must be configurable per device or test scenario.

### 7. Implement a Reusable Device Registry

The application must allow me to add and manage simulated devices in the future without editing Python source code.

Provide a persistent device registry that supports:

- Adding a new simulated device.
- Editing its configuration.
- Removing or disabling a simulated device.
- Associating it with an AC.
- Configuring its serial number.
- Configuring its authentication information securely.
- Starting and stopping its simulation.
- Viewing its current simulated state.
- Viewing its communication and command history.

When I create a new AC or IoT device in the web portal, I should be able to register its corresponding simulator configuration through a documented CLI command or an easy-to-use management interface.

Use a suitable local persistence mechanism, such as SQLite, for the Python testing application's own registry and test history. This local database must not replace or modify the AC Management backend database.

Never store production credentials in plain text or commit secrets to source control.

### 8. Build a Reusable API Client

Create a centralized Python API client for communicating with the existing AC Management backend.

It must:

- Use the actual discovered API endpoints.
- Support configurable base URLs.
- Handle device-specific authentication.
- Support request timeouts.
- Validate response status codes.
- Parse and validate JSON responses.
- Handle API errors consistently.
- Provide structured request/response logging with secrets redacted.
- Support multiple simulated devices.
- Avoid duplicating HTTP communication logic across test scripts.

Keep environment-specific configuration outside source code.

Provide a `.env.example` with the necessary configuration keys, but never place actual secrets in it.

### 9. Build the Automated Test Runner

Implement a proper test runner that can execute the test sets created in the initial test strategy.

Use appropriate Python testing tools, preferably `pytest` for automated testing, along with suitable fixtures and parametrized scenarios.

The runner should support:

- Running the complete test suite.
- Running an individual test set.
- Running a specific test case by ID.
- Running tests for one selected device.
- Running tests across multiple devices.
- Running selected end-to-end scenarios.
- Repeating tests for reliability checks.
- Producing machine-readable and human-readable reports.

Separate unit tests, API integration tests, device simulation tests, and end-to-end tests.

Use actual backend integration for end-to-end tests. Do not mark a test as an end-to-end success merely because a mocked API returned the expected response.

Use mocks only where appropriate for isolated unit tests and controlled failure-injection scenarios.

### 10. Most Important: Full End-to-End Verification

This is the primary acceptance requirement.

You must actually run the complete workflow through the existing web application and backend APIs.

**Example: Power ON**

1. Start the existing AC Management backend and required services.
2. Start the Python device simulator.
3. Register and initialize a simulated device associated with an existing test AC.
4. Confirm that the device is polling every five seconds.
5. Open the actual web portal.
6. Select the corresponding AC.
7. Click the ON control.
8. Verify that the backend receives and persists the requested command.
9. Verify that the simulated device retrieves the command on its next polling cycle.
10. Verify that the simulator processes and executes the ON command.
11. Verify that the simulator reports acknowledgement and execution result.
12. Verify that the simulator reports its updated power state.
13. Verify that the backend stores the device-reported state correctly.
14. Verify that the web portal displays the updated ON state.
15. Record timestamps for each stage so the complete workflow can be measured.

**Example: Power OFF**

Repeat the same process for OFF, verifying every stage independently.

**Example: Temperature Adjustment**

1. Select an AC in the web portal.
2. Increase or decrease its temperature setpoint.
3. Verify the backend command and stored value.
4. Wait for the device's next polling cycle.
5. Verify command retrieval.
6. Verify simulated temperature adjustment.
7. Verify acknowledgement and execution result.
8. Verify the reported setpoint in the backend.
9. Verify the updated temperature in the web portal.

Repeat for multiple temperature values and boundary conditions.

**Do not stop at command retrieval.** The device must execute the command, report its result, and the portal must eventually display the device-reported state.

The expected five-second polling interval does not guarantee that the entire workflow will finish in exactly five seconds. Measure and report actual timings for polling, retrieval, execution, acknowledgement, backend persistence, and frontend refresh.

### 11. Verify Every API and Every Feature

Create a complete API test matrix based on the actual backend implementation.

For each relevant API, verify:

- HTTP method and endpoint.
- Authentication requirements.
- Request parameters and payload.
- Successful response.
- Error response.
- Database side effects.
- Device-level consequences.
- Command lifecycle implications.
- Security and tenant isolation.
- Relevant frontend consequences.

For every major AC operation, test both successful and unsuccessful scenarios.

Do not leave untested endpoints or features that are part of the device communication lifecycle. If an endpoint cannot be tested because the required backend capability is missing, identify the missing functionality and explain it clearly.

### 12. Testing Dashboard / Management Interface

Provide a practical way to operate the Python testing platform manually.

At minimum, the application must provide clear commands or a management interface for:

- Registering a simulated device.
- Listing registered devices.
- Editing device configuration.
- Starting and stopping a device simulator.
- Checking device simulation status.
- Triggering individual test scenarios.
- Running complete test suites.
- Viewing command history.
- Viewing polling history.
- Viewing acknowledgement and execution results.
- Viewing recent errors.
- Generating test reports.

The interface must make it easy to add a newly created AC/device in the future.

A CLI-based management interface is acceptable if it is well-organized and easy to use. If a lightweight local dashboard materially improves usability, you may implement one, but keep it separate from the existing AC Management Portal.

### 13. Logging, Monitoring, and Test Reports

Implement structured logging throughout the project.

Capture:

- Test execution ID.
- Test case ID.
- Device serial number.
- AC identifier.
- Organization identifier where relevant.
- Polling request timestamp.
- Command retrieval timestamp.
- Command identifier.
- Command type.
- Command payload, with sensitive information excluded.
- Acknowledgement timestamp.
- Simulated execution timestamp.
- Device-reported state.
- Backend response.
- Test result.
- Error details.

Generate detailed test reports that include:

- Total tests.
- Passed tests.
- Failed tests.
- Skipped tests.
- Test duration.
- Device-wise results.
- API-wise results.
- End-to-end workflow results.
- Failed test details.
- Expected versus actual results.
- Relevant request/response evidence.
- Timing measurements for the command lifecycle.

Support suitable output formats such as HTML, JSON, and JUnit XML.

Reports must be easy to find in a dedicated reports directory and must not expose authentication secrets.

### 14. Data Integrity and Testing Safety

The testing application must operate against a clearly configured development/testing environment.

Never run destructive tests against production data.

Do not delete or overwrite existing AC records, organizations, device assignments, or user accounts as part of testing.

Create or use dedicated test devices and test AC records where appropriate.

If test data needs to be created through the existing backend, use its supported APIs and follow the existing data model.

Maintain clear separation between:
- Command requested by the web application.
- Command persisted by the backend.
- Command retrieved by the device.
- Command acknowledged by the device.
- Simulated execution result.
- Device-reported state.
- State displayed by the portal.

Do not incorrectly report a command as physically executed. This project simulates device execution and verifies backend communication; it does not physically transmit infrared signals to an AC. Actual hardware and physical AC execution require separate hardware-in-the-loop verification.

Likewise, a device acknowledgement must not automatically be treated as proof that the physical AC changed state.

### 15. Documentation and Future Reusability

Deliver complete project documentation, including:

- Project README.
- Installation and environment setup.
- Configuration reference.
- API integration guide.
- Device registration guide.
- Device simulator guide.
- Test strategy and test-case catalogue.
- Automated test execution guide.
- Manual testing guide.
- Test report interpretation.
- Troubleshooting guide.
- Architecture overview.
- Known limitations and future improvements.

Include practical examples for registering a new device and running its tests.

The documentation must explain exactly how I can use this project when a new AC is added to the existing web portal.

The project must be reusable without modifying source code every time a new device is introduced.

### 16. Implementation Workflow

Follow this implementation sequence:

**Phase 1 — Repository Discovery**
- Inspect the existing AC Management repository and documentation.
- Verify the Python project destination.
- Understand the actual device communication API and database workflow.
- Identify missing or incomplete backend functionality.

**Phase 2 — Test Planning**
- Prepare the complete test strategy.
- Create the test-case inventory and API matrix.
- Define expected results and acceptance criteria.
- Identify required test devices and data.

**Phase 3 — Python Project Foundation**
- Create the isolated Python project.
- Set up the virtual environment and dependencies.
- Implement configuration, logging, local registry, and API client.

**Phase 4 — Device Simulation**
- Implement independently running simulated devices.
- Implement five-second configurable polling.
- Implement command retrieval, execution, acknowledgement, and state reporting.
- Implement retries, failure injection, and recovery.

**Phase 5 — Automated Testing**
- Implement the complete test suite.
- Cover individual APIs and device operations.
- Cover multi-device scenarios and error conditions.
- Implement full end-to-end tests.

**Phase 6 — Actual Execution and Verification**
- Start the existing AC Management application.
- Start the Python simulator.
- Execute the test scenarios against the real backend.
- Verify database updates and actual frontend state.
- Fix defects and rerun affected tests.
- Repeat until all achievable acceptance criteria pass.

**Phase 7 — Reusable Manual Testing Environment**
- Complete device registration and configuration management.
- Implement convenient test execution commands.
- Verify that a newly registered device can be tested without source-code changes.

**Phase 8 — Documentation and Final Delivery**
- Complete the README and supporting documentation.
- Generate final test reports.
- Provide a clear implementation and verification summary.

Do not stop after preparing the plan or creating basic project files. Continue through implementation, execution, debugging, and verification.

### 17. Important Engineering Instructions

- Read the existing project before making assumptions.
- Reuse the actual API contracts and backend workflows.
- Do not introduce unnecessary dependencies or duplicate backend functionality.
- Keep modules small, focused, and maintainable.
- Avoid hardcoded device identifiers and credentials.
- Use configurable polling intervals, timeouts, retries, and test settings.
- Ensure multiple devices can run independently.
- Preserve test reproducibility.
- Make test results traceable to individual commands and devices.
- Never silently skip a failed test.
- Never mark an unexecuted test as passed.
- Never claim physical hardware verification when only simulation has been performed.
- Do not modify the existing web portal unnecessarily. If a genuine backend defect prevents testing, identify it, explain the required correction, and implement only the necessary scoped fix while preserving existing functionality.
- Do not interfere with the two other Python projects in the parent directory.

If you encounter missing API functionality, implementation inconsistencies, or blockers, investigate them and document the exact issue. Do not invent a workaround that falsely reports success.

### 18. Final Acceptance Criteria

The project will be considered complete only when:

1. The Python project exists in the exact requested directory.
2. It has an isolated, working environment and complete documentation.
3. It can register and manage multiple simulated IoT devices.
4. Devices poll the actual AC Management API at a configurable five-second testing interval.
5. Devices retrieve only their assigned commands.
6. ON/OFF commands are simulated and acknowledged correctly.
7. Temperature adjustments are simulated and acknowledged correctly.
8. Updated device state is reported to the backend.
9. Backend persistence is verified.
10. The web portal displays the updated device-reported state.
11. Device authentication and isolation tests pass.
12. Error handling, retries, and recovery scenarios are verified.
13. Automated test reports are generated.
14. A newly added AC/device can be registered and tested without changing Python source code.
15. The manual testing workflow is documented and usable.
16. All test failures are resolved where the existing system supports the required behavior, and any remaining backend or hardware limitations are explicitly documented.

### 19. Final Handover Report

At the end, provide a comprehensive but clear handover report containing:

- Project location and directory structure.
- Implemented modules and capabilities.
- Actual API endpoints integrated.
- Test sets and number of test cases.
- Total passed, failed, and skipped tests.
- End-to-end power ON/OFF results.
- End-to-end temperature adjustment results.
- Device polling and acknowledgement results.
- Backend/database verification results.
- Frontend verification results.
- Error and recovery test results.
- Known limitations or unresolved issues.
- Exact commands to start the application and run tests.
- Instructions for registering a new simulated IoT device.
- Location of generated reports and documentation.

Include evidence from actual test executions. Clearly distinguish fully verified results from partially verified features or scenarios that require physical hardware.

---

**Final instruction:** Take ownership of this task from initial inspection to final delivery. First understand the existing AC Management system, prepare the complete test plan, and then implement the Python IoT testing platform. Do not wait for approval after preparing the plan. Continue implementing and executing tests independently, investigate failures, fix the underlying issues where appropriate, and rerun tests until the defined acceptance criteria are satisfied. The objective is to deliver a genuinely working, reusable, professional-grade IoT testing platform—not just sample scripts or a demonstration.
