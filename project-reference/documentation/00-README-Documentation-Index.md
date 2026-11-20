# Centralized IoT HVAC Management Platform

## Documentation Pack --- Version 2.0

**Project type:** Client product\
**Documentation baseline:** Proposal / SRS / SDD v1.0 (6 November 2026),
updated with approved Variant 7 Manager Portal UI baseline\
**Status:** Detailed working baseline; open decisions remain subject to
client approval.

## Document audiences

This pack deliberately separates two documentation tracks.

### A. Product / professional documentation --- for client, product owner, stakeholders

-   `01-Project-Proposal.md`
-   `02-Software-Requirements-Specification.md`
-   `03-Software-Design-Description.md`

These explain what the product is, why it is needed, scope,
requirements, architecture, constraints, risks, and acceptance criteria
in a professional product-documentation style.

### B. Engineering / coding-agent implementation documentation

-   `04-Implementation-Guide.md`
-   `05-Frontend-Implementation.md`
-   `06-Backend-Implementation.md`
-   `07-API-Contract-Guidelines.md`
-   `08-Database-Design-Proposal.md`
-   `09-Device-and-IoT-Integration.md`
-   `10-Testing-Deployment-and-Operations.md`

These describe how to approach implementation. They are engineering
guidance, not immutable technology mandates. The coding agent may revise
implementation details when repository inspection, validated
requirements, hardware constraints, or technical evidence justify
changes. It must document consequential deviations and preserve the
product requirements and safety constraints.

## Required reading order for an implementation agent

1.  `00-README-Documentation-Index.md`
2.  `01-Project-Proposal.md` for product context
3.  `02-Software-Requirements-Specification.md` for required behavior
4.  `03-Software-Design-Description.md` for logical architecture and
    constraints
5.  `04-Implementation-Guide.md`
6.  Relevant specialist guides
    (frontend/backend/API/database/device/testing)
7.  The separate Variant 7 UI handoff pack:
    -   `01-theme-book.md`
    -   `02-ui-screen-plan.md`
    -   `03-screen-specifications.md`

## Source-of-truth and status conventions

-   **Confirmed:** explicitly agreed in the requirements discussions or
    source baseline.
-   **Proposed:** recommended engineering design; may be changed after
    validation.
-   **Open:** needs client, product-owner, or technical decision.
-   **Limitation:** cannot be guaranteed by the current architecture
    alone.
-   **Future:** intentionally outside the first release unless approved.

If a later client decision conflicts with this baseline, record the
decision, date, owner, impact, and affected requirements before changing
implementation.

## Important source baseline

The prior consolidated document was
`AC_Management_Platform_Project_Documentation_v1.docx`, a 17-page
combined Proposal, SRS, SDD, and implementation plan. This pack expands
and separates that baseline rather than silently replacing its confirmed
requirements.

## Key project truths

-   Multi-tenant centralized AC management platform.
-   Browser-based organization Manager Portal.
-   One Wi-Fi ESP32 per AC in the initial hardware model, with IR
    transmitter and receiver.
-   Multiple manufacturers/models require exact, validated IR profiles.
-   Original remote signals may be captured and synchronized when
    supported and permitted.
-   Hierarchical remote policy: Inherit / Enabled / Disabled, nearest
    explicit value wins, with AC override.
-   ON/OFF history, ON duration, temperature setpoint history, and mode
    history where supported.
-   Electricity/kWh measurement is out of current scope.
-   IR transmission does not prove physical AC execution.
-   Software policy at the ESP32 cannot physically block a direct
    handheld-remote IR beam.
-   Initial portal user role: Organization Administrator/Manager;
    complex RBAC deferred.
-   Variant 7 is the approved Manager Portal visual baseline.
