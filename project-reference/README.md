# Centralized IoT HVAC Management Platform — Project Reference Pack

**Package:** Project Reference Only  
**Documentation baseline:** Documentation Pack v2.0  
**UI baseline:** Variant 7 (approved)  
**Project implementation stack selected by client:** PHP, Laravel, MySQL  
**Scope:** Organization-facing Manager Portal and supporting backend/API/IoT integration guidance  
**Status:** Working baseline; client/technical open decisions remain explicitly identified in the documents.

This directory contains project reference materials only. It is not a Laravel application and does not contain application source code.

## Directory map

- `documentation/` — Product, requirements, architecture, implementation, backend, frontend, API, database, IoT, testing, deployment and operations documents.
- `ui-design/variant-7/` — Approved Variant 7 theme book, screen plan, detailed screen specifications, and the exact approved 10-screen composite image.
- `additional-references/` — Earlier consolidated Proposal/SRS/SDD/Implementation baseline retained for traceability.
- `MASTER_IMPLEMENTATION_PROMPT.md` — Instructions for the coding agent to inspect this pack, inspect the Laravel repository, prepare a plan/checklist, and then implement the complete application.

## Mandatory reading order for a coding agent

1. `documentation/00-README-Documentation-Index.md`
2. `documentation/01-Project-Proposal.md`
3. `documentation/02-Software-Requirements-Specification.md`
4. `documentation/03-Software-Design-Description.md`
5. `documentation/04-Implementation-Guide.md`
6. Relevant specialist guides in `documentation/` (frontend, backend, API, database, device/IoT, testing/deployment).
7. `ui-design/variant-7/01-theme-book.md`
8. `ui-design/variant-7/02-ui-screen-plan.md`
9. `ui-design/variant-7/03-screen-specifications.md`
10. Inspect `ui-design/variant-7/variant-7-approved-10-screen-composite.png` directly.

## Important interpretation rules

- Variant 7 is the approved visual baseline for the organization-facing Manager Portal.
- The SRS and SDD are the functional and technical source of truth; mockup text/data is illustrative.
- The client-selected implementation stack is PHP + Laravel + MySQL. Inspect the actual repository and preserve its installed compatible versions and conventions.
- The initial portal is for organization managers. A full platform-owner/developer administration portal is outside the initial scope.
- The product is multi-tenant; server-side tenant isolation is mandatory.
- One Wi-Fi ESP32 with IR transmitter and receiver is associated with each AC in the initial hardware model.
- IR transmission success is not proof of physical AC execution.
- Device online status is not the same as AC ON status.
- Temperature means AC setpoint, not room temperature.
- ON duration is not electricity consumption. kWh/power metering is outside current scope.
- ESP32 software cannot block a direct handheld remote-to-AC IR beam.
- Exact IR compatibility is model/remote-variant-specific and requires validation.
- The proposed database schema is adaptable; preserve domain invariants and document material changes.

## Reference image provenance

The image in `ui-design/variant-7/variant-7-approved-10-screen-composite.png` is the approved Variant 7 composite selected in the prior design iteration. It contains ten Manager Portal screens and the card-based Locations Explorer. Do not substitute an older variant with a floor-plan/map-style Locations Explorer.

## Suggested use

Place this entire `project-reference/` directory inside the root of the existing Laravel project. Run the coding agent from the Laravel project root and provide it with `MASTER_IMPLEMENTATION_PROMPT.md` as the task prompt.
