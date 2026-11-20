# AC Management Portal --- Detailed Screen Specifications

Version 1.0 \| Baseline: Variant 7

## Global implementation rules

Follow `01-theme-book.md` for styling and `02-ui-screen-plan.md` for
scope/navigation. Use shared shell. Treat mockup data as illustrative.
Every data-driven page needs loading, empty, error, partial-data, and
stale-data states. Do not invent physical confirmation, room
temperature, or electricity consumption.

`<a id="scr-01-overview-dashboard">`{=html}`</a>`{=html} \## SCR-01 ---
Overview Dashboard **Purpose:** Organization-wide operational snapshot.
**Layout:** 1. Page title/subtitle. 2. Site selector and date filter
(default Today). 3. KPI cards: Total ACs, reported ON, reported OFF,
Offline devices, Today's ON Duration. 4. AC state distribution chart. 5.
Operating-hours trend chart. 6. ACs by building/location summary. 7.
Active alerts. 8. Floor/building summary cards and recent activity.
**Actions:** Change filters; open location; open AC details/control;
open relevant alert. **Rules:** Separate AC state from ESP32
connectivity. Unknown/stale must not be counted as OFF without explicit
labeling. ON Duration is not kWh.

`<a id="scr-02-locations-explorer">`{=html}`</a>`{=html} \## SCR-02 ---
Locations Explorer **Purpose:** Browse organization hierarchy and
understand AC distribution. **Mandatory visual direction:** Simple
card-based layout inspired by Variant 4/7. No CAD, Sweet Home 3D,
editable floor plan, or room drawing as primary UI. **Layout:** 1.
Title, search, optional Tree View/Card View selector. 2. Two columns:
collapsible hierarchy on left; selected location content on right. 3.
Breadcrumb and selected location heading. 4. Summary cards: Total ACs,
ON, OFF, Today's ON Duration. 5. Grid of space cards; each shows space
name, AC count, ON/OFF/unknown summary, optional ON duration.
**Hierarchy:** Organization → Sites → Buildings → Floors → Spaces.
Spaces can be rooms, corridors, halls, galleries, sections, or other
named areas. **Actions:** Expand/collapse nodes; select node; search;
select space card to open Inventory filtered to that space; select AC to
open Details/Control.

`<a id="scr-03-air-conditioners-inventory">`{=html}`</a>`{=html} \##
SCR-03 --- Air Conditioners Inventory **Purpose:** Search, filter, and
select AC units. **Layout:** Title; filters for
site/building/floor/space/state; search by name/ID/model/location;
optional export/advanced filters; Grid/Table view selector. Grid cards
show AC identity, location, manufacturer/model, reported state,
setpoint, today's ON Duration, device connectivity. Table columns mirror
these fields plus actions. **Actions:** Search/filter; switch view; open
Details; open Control. **Rules:** Unknown setpoint is "---"/Unknown, not
0°C. Device connectivity is separate. No kWh.

`<a id="scr-04-ac-control-panel">`{=html}`</a>`{=html} \## SCR-04 --- AC
Control Panel **Purpose:** Send supported basic commands and show
command progress/last reported state. **Layout:** Header with back, AC
ID/name, location breadcrumb, device connectivity, last update. Main
composition: AC illustration panel; prominent vertical rocker (ON top,
OFF bottom); temperature panel with large °C setpoint and minus/plus
buttons. Mode/fan only if truly supported and approved. **Actions:**
Request ON/OFF; adjust setpoint by 1°C within supported profile range;
open Details. **Feedback:** Sending; accepted/queued; device
acknowledgement (only if available); IR transmission result (if
reported); failed/timed out; last reported state/time. **Rules:** Do not
optimistically claim physical state changed on click. Successful IR send
does not prove AC execution. If offline, explain delivery limitation;
only claim queueing if implemented.

`<a id="scr-05-ac-details-and-status">`{=html}`</a>`{=html} \## SCR-05
--- AC Details & Status **Purpose:** Inspect identity, location, model,
connectivity, last reported configuration, and recent activity.
**Layout:** Header with AC name/ID/location/state. Identity card:
location, manufacturer, model, installation date if available, device
connectivity/last seen. Tabs: Overview, History, Usage, manager-safe
Settings/Policy. Overview tiles: last reported state, setpoint, today's
ON Duration, ESP32 connectivity. Recent activity list. **Actions:** Open
Control; switch tabs; open Activity History filtered to AC; open
Analytics filtered to AC. **Rules:** Label setpoint; use "Last reported"
when appropriate. Device online ≠ AC ON. Identify physical remote as
source when known.

`<a id="scr-06-usage-and-analytics">`{=html}`</a>`{=html} \## SCR-06 ---
Usage & Analytics **Purpose:** Analyze operating duration and compare
patterns. **Layout:** Site/date filters (Today, Yesterday, 7 days, 30
days, Custom); tabs Usage Overview, Temperature Trend, Location-wise,
AC-wise; KPI cards Total ON Duration, average ON Duration per AC,
most-used location, average setpoint if meaningful; daily ON Duration
chart; usage by building comparison; drill-down list. **Actions:**
Change filters/tabs; drill into location/AC; open Activity History.
**Rules:** Units hours/minutes. No kWh, electricity cost, or
energy-savings claims. Setpoint is not room temperature.

`<a id="scr-07-activity-history">`{=html}`</a>`{=html} \## SCR-07 ---
Activity History **Purpose:** Chronological, filterable operational
event log. **Layout:** Filters for site/location, AC, action, source,
date range, search. Table columns: date/time, AC, location,
action/event, source (Web Portal/Physical Remote/System), result if
available. Pagination or incremental loading. **Actions:**
Filter/search; open AC Details; inspect event; export only if
implemented. **Rules:** Show timezone consistently. Distinguish event
time from server-received time if both exist. A command request is not a
confirmed state change.

`<a id="scr-08-reports">`{=html}`</a>`{=html} \## SCR-08 --- Reports
**Purpose:** Generate operational reports. **Layout:** Report cards:
Daily Usage, Weekly Usage, Monthly Usage, Location-wise, AC-wise
History, Temperature Setpoint. Selecting a card opens configuration:
date/period, site/building/floor/space, optional AC, supported output
format. Show generated reports with timestamp, scope, status, download.
**Actions:** Select report; configure; generate; download. **Rules:**
Report operating duration, not electricity consumption. Label
temperature as setpoint. Do not show unsupported formats as active.

`<a id="scr-09-notifications">`{=html}`</a>`{=html} \## SCR-09 ---
Notifications **Purpose:** Review issues and important updates.
**Layout:** Tabs All, Alerts, Warnings, Information. List item:
severity, title, AC/location, timestamp, read state, optional overflow.
Optional date/location/AC filters. **Types:** ESP32 offline/reconnected;
command delivery/transmission failure; stale status; physical remote
activity/attempt only when detectable; command accepted/completed
informational event. **Actions:** Open; mark read; mark all read; open
AC Details or Activity History. **Rules:** Never claim software
physically blocked a remote beam. Use "attempt detected" only if
actually detectable.

`<a id="scr-10-organization-settings">`{=html}`</a>`{=html} \## SCR-10
--- Organization Settings **Purpose:** Basic organization preferences
for the initial manager role. **Layout:** Navigation cards or grouped
sections: - Organization Information: display name, basic details,
timezone. - Notification Preferences: supported categories/channels. -
Physical Remote Policy: inherited organization policy and explanation of
lower-level/AC overrides where supported; values Inherit, Enabled,
Disabled. - Account & Profile: profile basics and security link if
supported. **Actions:** Edit permitted fields; save/cancel. **Rules:**
Explain that software policy does not physically prevent a handheld
remote from directly controlling the AC. Do not expose raw device
settings, MQTT credentials, IR profiles, or platform admin controls.
Avoid advanced RBAC in MVP.

## Shared states and interaction rules

-   Every page: loading, empty, error, partial, stale/unknown, success
    feedback.
-   Reuse site/building/floor/space/AC/date filters and preserve context
    where practical.
-   Command lifecycle must distinguish manager request, backend
    acceptance/queue, device receipt, IR transmission result, last
    reported AC state, and freshness.
-   Active MVP controls only ON/OFF and temperature setpoint. Keep
    mode/fan/swing/timers/schedules/automation out of active UI until
    validated.
