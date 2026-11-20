# AC Management Portal --- UI Screen Plan

Version 1.0 \| Baseline: Variant 7 \| Scope: Organization-facing Manager
Portal

## 1. Purpose

High-level screen inventory, navigation, scope, and relationships. Read
`01-theme-book.md` for visual rules and `03-screen-specifications.md`
for detailed screen layouts and behavior.

## 2. Product scope

Managers can browse organization locations and ACs, view last reported
states, send ON/OFF and temperature commands, inspect
operating-duration/setpoint history, investigate connectivity/command
issues, review notifications, and generate operational reports.

Not included: platform/developer admin, tenant provisioning, firmware
administration, MQTT administration, raw IR learning/profile editing,
manufacturing workflows, electricity metering, complex floor-plan
editor, advanced RBAC.

## 3. Main navigation

    Order Item                Suggested route     Screen
  ------- ------------------- ------------------- --------
        1 Overview            /overview           SCR-01
        2 Locations           /locations          SCR-02
        3 Air Conditioners    /air-conditioners   SCR-03
        4 Usage & Analytics   /analytics          SCR-06
        5 Activity History    /activity           SCR-07
        6 Reports             /reports            SCR-08
        7 Notifications       /notifications      SCR-09
        8 Settings            /settings           SCR-10

SCR-04 Control and SCR-05 Details are contextual screens opened from an
AC card/row or event.

## 4. Screen inventory

  -------------------------------------------------------------------------------------------------------------------------------------------
  ID             Screen          Purpose                Entry points        Detailed spec
  -------------- --------------- ---------------------- ------------------- -----------------------------------------------------------------
  SCR-01         Overview        Organization-wide      Default landing     `03-screen-specifications.md#scr-01-overview-dashboard`
                 Dashboard       snapshot                                   

  SCR-02         Locations       Browse location        Sidebar/dashboard   `03-screen-specifications.md#scr-02-locations-explorer`
                 Explorer        hierarchy and AC                           
                                 distribution                               

  SCR-03         AC Inventory    Search/filter/select   Sidebar/location    `03-screen-specifications.md#scr-03-air-conditioners-inventory`
                                 ACs                                        

  SCR-04         AC Control      Send basic commands    AC card/details     `03-screen-specifications.md#scr-04-ac-control-panel`
                 Panel                                                      

  SCR-05         AC Details &    Inspect AC metadata,   AC details action   `03-screen-specifications.md#scr-05-ac-details-and-status`
                 Status          connectivity, events                       

  SCR-06         Usage &         Analyze operating      Sidebar/dashboard   `03-screen-specifications.md#scr-06-usage-and-analytics`
                 Analytics       duration/setpoint                          
                                 trends                                     

  SCR-07         Activity        Chronological AC event Sidebar/recent      `03-screen-specifications.md#scr-07-activity-history`
                 History         log                    activity            

  SCR-08         Reports         Generate operational   Sidebar             `03-screen-specifications.md#scr-08-reports`
                                 reports                                    

  SCR-09         Notifications   Review                 Sidebar/top-bar     `03-screen-specifications.md#scr-09-notifications`
                                 alerts/warnings/info   bell                

  SCR-10         Organization    Basic organization     Sidebar/profile     `03-screen-specifications.md#scr-10-organization-settings`
                 Settings        preferences                                
  -------------------------------------------------------------------------------------------------------------------------------------------

## 5. Shared shell

All screens use deep-navy sidebar, snowflake/product identity, compact
top bar, ice-blue page background, white cards, and tokens from the
theme book. Sidebar order is defined above.

## 6. User journeys

-   Find/control: Overview → Locations or Inventory → AC Details/Control
    → review command feedback.
-   Browse location: Locations → select site/building/floor/space →
    select AC → Details/Control.
-   Review usage: Analytics → date/location filter → drill down →
    Activity History.
-   Investigate: Notification/dashboard alert → AC Details → recent
    events → Control if needed.
-   Report: Reports → choose report → set scope/date → generate →
    download.

## 7. Priorities

P1: SCR-01, SCR-03, SCR-04. P2: SCR-02, SCR-05, SCR-06. P3: SCR-07,
SCR-08. P4: SCR-09, SCR-10.

## 8. Cross-screen rules

-   All data is tenant/organization scoped.
-   Reuse site/building/floor/space/date filters consistently.
-   Preserve selected AC and location context between
    details/control/history.
-   Provide loading, empty, error, partial, and stale states.
-   Do not imply electricity measurement or physical confirmation.
-   AC state and ESP32 connectivity are separate.

## 9. MVP controls

Active controls: ON/OFF and temperature setpoint. Initial step 1°C,
bounded by supported model/profile range. Mode, fan, swing, timer,
schedules, and automation remain future scope unless explicitly
validated.

## 10. Domain rules

One ESP32 per AC. Device online does not prove AC ON. Setpoint is not
room temperature. IR send success is not physical confirmation. ON
Duration is not kWh. Unknown/stale is not OFF. Physical remote policy
cannot block the remote's direct IR beam.

## 11. Handoff map

-   `01-theme-book.md`: design system.
-   `02-ui-screen-plan.md`: this screen inventory and navigation.
-   `03-screen-specifications.md`: detailed screen specs. Read theme
    book → screen plan → relevant screen specification before
    implementation.
