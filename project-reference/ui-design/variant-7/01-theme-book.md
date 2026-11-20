# AC Management Portal --- Theme Book

Version 1.0 \| Approved baseline: Variant 7

## 1. Design direction

Use Variant 7 as the approved visual source of truth: cold, clean,
icy-blue and snow-white surfaces, deep-navy navigation, professional
blue accents, subtle cyan highlights, restrained gradients, soft
shadows, rounded cards, and compact enterprise layouts. The portal is
for organization managers, not platform developers.

Design principles: cool climate identity; operational clarity; low
complexity; readable information density; consistent reusable
components. Do not use CAD/Sweet Home 3D-style floor plans. Locations
must use hierarchy plus simple summary/space cards.

## 2. Color tokens

  Token            Hex       Use
  ---------------- --------- ----------------------
  navy-950         #082B4C   Deep navigation
  navy-900         #0B355B   Sidebar
  navy-800         #124A78   Hover/secondary
  blue-700         #0875C9   Primary actions
  blue-600         #1689E8   Main accent
  blue-500         #299FF2   Charts/links
  blue-100         #DCEEFF   Selected surfaces
  blue-50          #F1F8FF   Soft surfaces
  ice-100          #E7F4FF   Frosted panels
  ice-50           #F6FBFF   Page background
  white            #FFFFFF   Cards/inputs
  text-primary     #102A43   Main text
  text-secondary   #52677D   Supporting text
  text-muted       #7D91A5   Metadata
  border           #D8E7F4   Card/input borders
  divider          #E8F0F7   Separators
  success          #20B96B   Known/reported ON
  success-soft     #E4F8ED   Success badge
  danger           #E34D59   Failures/destructive
  danger-soft      #FDEBED   Error background
  warning          #F2A52B   Warning/stale
  warning-soft     #FFF5DF   Warning background
  info             #299FF2   Information
  neutral          #8798A9   Unknown/neutral
  neutral-soft     #EDF2F6   Neutral badge

Blue is the primary interaction color; navy is structural. Keep
backgrounds very light. Green means reported ON/success, not physical
verification. Grey means OFF only when known; unknown must be labeled
Unknown. Amber means warning/stale. Red means failure/destructive. Never
rely on color alone.

## 3. Typography

Preferred font: Inter; fallback to system sans-serif. \| Element \| Size
\| Weight \| \|---\|---:\|---:\| \| Page title \| 24--28px \| 700 \| \|
Section title \| 17--20px \| 650--700 \| \| Card title \| 14--16px \|
600 \| \| Body \| 13--14px \| 400--500 \| \| Table \| 12--13px \|
450--500 \| \| Caption \| 11--12px \| 400 \| \| KPI value \| 24--30px \|
700 \| Use sentence case, readable contrast, and consistent line
heights.

## 4. Application shell

Desktop-first, tablet-responsive. Persistent deep-navy left sidebar;
compact top bar; icy page background; white card surfaces. Sidebar
order: 1. Overview 2. Locations 3. Air Conditioners 4. Usage & Analytics
5. Activity History 6. Reports 7. Notifications 8. Settings

Top bar: page title/subtitle left; relevant site/date filters;
notification and profile controls right. Do not create a platform-admin
panel inside this portal.

## 5. Spacing and geometry

Use an 8px rhythm. \| Token \| Value \| \|---\|---:\| \| space-1 \| 4px
\| \| space-2 \| 8px \| \| space-3 \| 12px \| \| space-4 \| 16px \| \|
space-5 \| 20px \| \| space-6 \| 24px \| \| space-8 \| 32px \| Cards:
10--14px radius, 1px subtle border, restrained shadow, 16--20px padding.
Avoid excessive whitespace and excessive nesting.

## 6. Components

-   Primary button: blue fill, white text; secondary: white/light-blue
    with border; destructive: red only for destructive actions.
-   Inputs: white, subtle border, 8--10px radius, visible label and blue
    focus ring.
-   Tables: compact, light header, subtle row dividers, light-blue
    hover, pagination for long lists.
-   Badges: ON green; OFF neutral; Offline amber/red by severity;
    Unknown/Stale neutral/amber; failure red. Always include text.
-   Charts: blue/cyan series, light gridlines, clear axes and units,
    exact-value tooltips. Use hours/minutes for operating duration. No
    kWh charts.
-   Icons: one consistent line-icon family. Snowflake/climate mark is
    the product identity.
-   AC illustrations may be used in control/detail contexts; avoid
    decorative imagery that competes with data.

## 7. AC control styling

Control screen must show AC identity/location, connectivity and last
update; AC illustration; prominent vertical two-position rocker (ON top,
OFF bottom); large setpoint in °C; separate minus/plus buttons. Initial
step: 1°C within the model-supported range. Mode/fan/swing/timer
controls must not appear functional unless supported and approved.

Command feedback must distinguish sending, accepted/queued, device
acknowledgement, IR transmission result, failed/timed out, and last
reported state. IR transmission success does not prove the AC physically
changed state.

## 8. Responsive behavior

Desktop: full sidebar and multi-column cards. Tablet: collapsed icon
rail/reduced columns. Mobile: drawer navigation, wrapped filters,
stacked cards, and usable control targets (minimum \~40--44px). Tables
may become stacked rows. Avoid horizontal overflow.

## 9. Accessibility and motion

Keyboard reachable controls, visible focus, descriptive labels, adequate
contrast, text plus color for states, reduced-motion support. Use subtle
short transitions only. No animated snow, heavy blur, or continuous
decorative motion.

## 10. Domain semantics (mandatory)

-   ON Duration is operating duration, not electricity consumption.
-   Temperature is the AC setpoint, not room temperature.
-   ESP32 online does not prove AC is running.
-   Distinguish AC state from device connectivity.
-   Distinguish command requested, device receipt, IR transmission
    result, and last reported state.
-   Unknown/stale is not OFF.
-   Physical remote software policy cannot physically block a remote's
    direct IR beam.

## 11. Terminology

Use: Air Conditioner/AC, Location, Site/Building/Floor/Space, ON/OFF,
Set Temperature, ON Duration/Operating Hours, Device Online/Offline,
Last Reported/Last Updated, Physical Remote Policy. Avoid:
kWh/electricity usage, room temperature without a room sensor, confirmed
physical state without feedback, raw IR/MQTT/ESP32 internals in manager
UI.

## 12. Do / Don't

Do follow Variant 7; use consistent tokens; keep Locations card-based;
make controls obvious; label uncertainty. Don't reintroduce CAD floor
plans; use turquoise as dominant accent; add kWh; expose developer
tools; imply physical confirmation; overdecorate.
