<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Domain configuration
    |--------------------------------------------------------------------------
    | All values that influence domain behavior are centralized here. The
    | default remote policy is deliberately explicit because the reference SRS
    | records "Disabled" as a recommendation that still needs client approval.
    */
    'device_offline_after_minutes' => (int) env('HVAC_DEVICE_OFFLINE_AFTER_MINUTES', 5),
    'state_stale_after_minutes' => (int) env('HVAC_STATE_STALE_AFTER_MINUTES', 120),
    'command_ttl_seconds' => (int) env('HVAC_COMMAND_TTL_SECONDS', 300),
    'heartbeat_interval_seconds' => (int) env('HVAC_HEARTBEAT_INTERVAL_SECONDS', 5),
    'default_remote_policy' => env('HVAC_DEFAULT_REMOTE_POLICY', 'disabled'),
    'temperature_default_step' => 1,

    'simulator' => [
        // Development-only. Never enabled in production environments.
        'auto_respond' => (bool) env('HVAC_SIMULATOR_AUTO_RESPOND', false),
        'label' => 'Development simulator',
    ],

    'notification_categories' => [
        'device_offline' => 'Device offline',
        'device_reconnected' => 'Device reconnected',
        'command_failure' => 'Command failures and timeouts',
        'stale_state' => 'Stale AC state',
        'physical_remote' => 'Physical remote activity',
        'unknown_ir' => 'Unknown IR signals',
        'command_completed' => 'Command completed (informational)',
    ],

    'report_types' => [
        'daily_usage' => 'Daily Usage Report',
        'weekly_usage' => 'Weekly Usage Report',
        'monthly_usage' => 'Monthly Usage Report',
        'location_usage' => 'Location-wise Usage Report',
        'ac_history' => 'AC ON/OFF History Report',
        'setpoint_history' => 'Temperature Setpoint History Report',
        'device_connectivity' => 'Device Connectivity Report',
    ],

    'usage' => [
        // Provisional ON intervals are included in totals but always flagged.
        'include_provisional' => true,
        // Unknown/stale gaps are never reported as OFF time.
        'unknown_is_not_off' => true,
    ],
];
