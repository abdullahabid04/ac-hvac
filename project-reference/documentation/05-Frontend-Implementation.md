# Frontend Implementation Guide

## Organization Manager Portal

**Version:** 2.0 \| **Visual baseline:** Variant 7

## 1. Scope

Implement only the organization-facing Manager Portal described in the
UI handoff. Platform/developer administration is outside this portal and
may be handled separately.

Read: - `01-theme-book.md` - `02-ui-screen-plan.md` -
`03-screen-specifications.md`

These three files are the authoritative visual and screen-level
reference.

## 2. Ten screens

  -----------------------------------------------------------------------
  ID                      Screen                  Main responsibility
  ----------------------- ----------------------- -----------------------
  SCR-01                  Overview Dashboard      AC estate overview,
                                                  status, usage duration,
                                                  alerts

  SCR-02                  Locations Explorer      Organization hierarchy
                                                  and space summary cards

  SCR-03                  Air Conditioners        Search/filter AC
                          Inventory               assets; grid/table
                                                  views

  SCR-04                  AC Control Panel        ON/OFF rocker and
                                                  setpoint controls

  SCR-05                  AC Details & Status     Asset, connectivity,
                                                  last-known state,
                                                  recent events

  SCR-06                  Usage & Analytics       Operating duration and
                                                  setpoint trends

  SCR-07                  Activity History        Chronological event
                                                  records and filters

  SCR-08                  Reports                 Select, configure,
                                                  generate, download
                                                  reports

  SCR-09                  Notifications           Alerts, warnings,
                                                  informational events

  SCR-10                  Organization Settings   Organization,
                                                  notification, remote
                                                  policy, profile
  -----------------------------------------------------------------------

## 3. Design system

Implement theme tokens centrally: - Icy white / very light blue
backgrounds. - Deep navy sidebar. - Blue primary actions, selection,
links, and chart series. - Green ON/success, neutral OFF/unknown, amber
warning/stale, red failures. - Inter preferred font. - 8px spacing
rhythm. - 10--14px card radius. - Restrained shadows and subtle borders.

Do not introduce a new palette per page. Avoid excessive gradients,
glassmorphism, animated snow, oversized decoration, and heavy
illustration.

## 4. Shared components

Build reusable components before screen-specific implementations: -
ApplicationShell / Sidebar / Topbar. - PageHeader. - SiteSelector /
DateRangeFilter. - LocationBreadcrumb. - StatusBadge with distinct
AC-state and device-connectivity variants. - MetricCard. - ACGridCard
and ACDataTable. - LocationTree and LocationSpaceCard. - RockerSwitch
(ON top, OFF bottom). - TemperatureSetpointControl. -
CommandLifecycleIndicator. - DeviceConnectivityIndicator. -
EventTimeline / ActivityTable. - AlertList / NotificationItem. -
ChartCard. - EmptyState / ErrorState / LoadingSkeleton /
StaleDataNotice. - ConfirmDialog / Toast / Pagination.

Components must receive data and callbacks; avoid embedding API calls in
visual primitives.

## 5. Navigation

Sidebar order: Overview, Locations, Air Conditioners, Usage & Analytics,
Activity History, Reports, Notifications, Settings.

AC Control and AC Details are contextual routes opened from AC
inventory, location cards, dashboard activity, or notifications.
Preserve selected AC context and return navigation.

## 6. Screen-specific guidance

### SCR-01 Overview

Use KPI row, status distribution, operating-hours trend, location
summary, active alerts, and recent activity. Filters must scope all
applicable cards. Do not label ON duration as energy.

### SCR-02 Locations

Use left hierarchy and right selected-location panel. Right panel
contains location summary KPIs and simple space cards. No CAD-like floor
plan or room editor. Spaces are typed generic areas, not room-only.

### SCR-03 Inventory

Grid View is the visual default; Table View is available for dense
operations. Filters include site, building, floor/space, state, and
search. Distinguish AC state from ESP32 connectivity.

### SCR-04 Control

Use AC identity/location, device status/last update, AC illustration,
prominent vertical rocker, and circular or large numeric temperature
control. ON top, OFF bottom. Plus/minus increments by 1°C within
profile-supported range. Do not make unsupported mode/fan/swing controls
active.

### SCR-05 Details

Use identity card plus tabbed Overview/History/Usage/Settings areas.
Show setpoint, last-reported state, today's ON duration, device
connectivity, and recent activity.

### SCR-06 Analytics

Date presets Today, Yesterday, Last 7 Days, Last 30 Days, Custom. Tabs:
Usage Overview, Temperature Trend, Location-wise, AC-wise. Use
hours/minutes. No kWh.

### SCR-07 Activity

Filter by date, location, AC, action, and source. Show event time and
source; result only where available.

### SCR-08 Reports

Report cards: Daily, Weekly, Monthly, Location-wise, AC-wise History,
Temperature Setpoint. Generate/download states must be real, not
decorative.

### SCR-09 Notifications

Tabs All, Alerts, Warnings, Information. Include read/unread and
navigation to affected AC. Do not claim direct remote signals were
physically blocked.

### SCR-10 Settings

Simple cards/sections: Organization Information, Notification
Preferences, Physical Remote Policy, Account & Profile. No raw
device/IR/broker settings.

## 7. Data semantics in UI

-   AC state: Known ON / Known OFF / Unknown / Stale.
-   Device connectivity: Online / Offline / Unknown.
-   Setpoint: temperature target, not room measurement.
-   ON duration: operating interval estimate, not kWh.
-   Command lifecycle: requested, published/queued, received, IR
    transmission result, failed/expired.
-   Show last-reported timestamp and data quality.

Do not optimistically switch reported state just because a manager
clicked ON/OFF. A separate pending command indicator may show the
requested action.

## 8. Responsive behavior

Desktop: full sidebar and multi-column content. Tablet: collapsed
sidebar or icon rail; reduce card columns. Mobile: drawer navigation,
stacked cards, filter drawer, responsive tables. Ensure rocker and
temperature controls remain usable without horizontal overflow.

## 9. Accessibility and UX

-   Visible keyboard focus.
-   Accessible names for icon-only controls.
-   Status text plus color/icon.
-   Adequate contrast.
-   Minimum touch target around 40--44px.
-   Respect reduced motion.
-   Confirm destructive operations; routine controls should not be
    burdened with unnecessary dialogs.
-   Provide loading, empty, error, stale, and success feedback.

## 10. Frontend testing

Test: - Navigation and context preservation. - Tenant-scoped data
display (never rely on UI for security). - Filters, search, sorting,
pagination. - Control pending/success/failure states. -
Unknown/stale/online/offline combinations. - Responsive breakpoints. -
Keyboard navigation and accessible labels.
