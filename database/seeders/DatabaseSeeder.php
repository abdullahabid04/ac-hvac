<?php

namespace Database\Seeders;

use App\Enums\CommandStatus;
use App\Enums\ConnectivityStatus;
use App\Enums\EventSource;
use App\Enums\NotificationSeverity;
use App\Enums\PolicyScope;
use App\Enums\PolicyValue;
use App\Enums\PowerState;
use App\Enums\StateQuality;
use App\Models\AcCommand;
use App\Models\AcDailyUsage;
use App\Models\AcModel;
use App\Models\AcStateHistory;
use App\Models\AcStateSnapshot;
use App\Models\AcUnit;
use App\Models\ActivityLog;
use App\Models\Building;
use App\Models\Device;
use App\Models\DeviceAssignment;
use App\Models\DeviceEvent;
use App\Models\Floor;
use App\Models\GeneratedReport;
use App\Models\IrProfile;
use App\Models\IrProfileVersion;
use App\Models\Notification;
use App\Models\NotificationPreference;
use App\Models\Organization;
use App\Models\RemotePolicy;
use App\Models\Site;
use App\Models\Space;
use App\Models\User;
use App\Services\DeviceAuthService;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AcCatalogueSeeder::class,
            DemoDataSeeder::class,
        ]);
    }
}

/**
 * Seeds the global AC catalogue (models + IR profile versions) shared as
 * reference data across organizations.
 */
class AcCatalogueSeeder extends Seeder
{
    public function run(): void
    {
        $models = [
            [
                'manufacturer' => 'Daikin',
                'model_name' => 'FTXM35TV16 Inverter',
                'remote_variant' => 'inverter',
                'protocol_name' => 'Daikin-NEC',
                'profiles' => [
                    [
                        'name' => 'Daikin FTXM35TV16 IR',
                        'version_number' => 2,
                        'capabilities' => ['power' => true, 'setpoint' => true, 'mode' => true, 'fan' => true, 'swing' => true],
                        'min' => 16, 'max' => 32, 'step' => 0.5,
                        'validation_status' => 'validated',
                    ],
                ],
            ],
            [
                'manufacturer' => 'Mitsubishi Electric',
                'model_name' => 'MSZ-LN35VG',
                'remote_variant' => 'premium',
                'protocol_name' => 'Mitsubishi-NEC',
                'profiles' => [
                    [
                        'name' => 'Mitsubishi MSZ-LN IR',
                        'version_number' => 1,
                        'capabilities' => ['power' => true, 'setpoint' => true, 'mode' => true, 'fan' => true, 'swing' => true],
                        'min' => 16, 'max' => 31, 'step' => 1,
                        'validation_status' => 'validated',
                    ],
                ],
            ],
            [
                'manufacturer' => 'LG',
                'model_name' => 'Artcool Deluxe',
                'remote_variant' => 'standard',
                'protocol_name' => 'LG-NEC',
                'profiles' => [
                    [
                        'name' => 'LG Artcool IR',
                        'version_number' => 1,
                        'capabilities' => ['power' => true, 'setpoint' => true, 'mode' => true, 'fan' => true, 'swing' => false],
                        'min' => 18, 'max' => 30, 'step' => 1,
                        'validation_status' => 'unvalidated',
                    ],
                ],
            ],
            [
                'manufacturer' => 'Samsung',
                'model_name' => 'AR09TXECAWK',
                'remote_variant' => 'standard',
                'protocol_name' => 'Samsung-NEC',
                'profiles' => [
                    [
                        'name' => 'Samsung AR09 IR',
                        'version_number' => 1,
                        'capabilities' => ['power' => true, 'setpoint' => true, 'mode' => true, 'fan' => true, 'swing' => true],
                        'min' => 16, 'max' => 30, 'step' => 1,
                        'validation_status' => 'validated',
                    ],
                ],
            ],
        ];

        foreach ($models as $modelData) {
            $profiles = $modelData['profiles'];
            unset($modelData['profiles']);

            $model = AcModel::factory()->create($modelData);

            foreach ($profiles as $profileData) {
                $min = $profileData['min'];
                $max = $profileData['max'];
                $step = $profileData['step'];

                $profile = IrProfile::factory()->create([
                    'ac_model_id' => $model->id,
                    'name' => $profileData['name'],
                ]);

                IrProfileVersion::factory()->create([
                    'ir_profile_id' => $profile->id,
                    'version_number' => $profileData['version_number'],
                    'capabilities' => $profileData['capabilities'],
                    'temperature_min_c' => $min,
                    'temperature_max_c' => $max,
                    'temperature_step_c' => $step,
                    'validation_status' => $profileData['validation_status'],
                    'validation_notes' => 'Reference profile validated against lab remote captures.',
                    'validated_at' => $profileData['validation_status'] === 'validated' ? now() : null,
                ]);
            }
        }
    }
}

/**
 * Seeds the demonstration tenant data: two organizations, users, hierarchy,
 * ACs, devices, policies, commands, events, history, activity, notifications,
 * usage aggregates and reports.
 */
class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // ------------------------------------------------------------------
        // Organization 1: Northwind (primary demo org)
        // ------------------------------------------------------------------
        $northwind = Organization::create([
            'name' => 'Northwind Traders',
            'slug' => 'northwind',
            'timezone' => 'Asia/Karachi',
            'status' => 'active',
            'contact_email' => 'facilities@northwind.test',
            'contact_phone' => '+92 21 111 222 333',
            'address' => 'Plot 12, Main Boulevard, Northwind Business Park, Karachi, Pakistan',
            'settings' => [],
        ]);

        $northwindAdmin = User::create([
            'name' => 'Northwind Administrator',
            'email' => 'admin@northwind.test',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'job_title' => 'Facilities Manager',
            'phone' => '+92 21 111 222 333',
        ]);
        $northwind->users()->attach($northwindAdmin->id, ['role' => 'organization_admin', 'is_active' => true]);

        // Notification preferences for the demo user
        foreach (config('hvac.notification_categories') as $key => $label) {
            NotificationPreference::create([
                'user_id' => $northwindAdmin->id,
                'key' => $key,
                'in_app' => true,
                'email' => in_array($key, ['device_offline', 'command_failure'], true),
            ]);
        }

        // Organization-wide remote policy (baseline: disabled/provisional)
        RemotePolicy::create([
            'organization_id' => $northwind->id,
            'scope_type' => PolicyScope::Organization->value,
            'scope_id' => $northwind->id,
            'policy_value' => PolicyValue::Disabled,
            'version' => 1,
            'updated_by' => $northwindAdmin->id,
        ]);

        // ------------------------------------------------------------------
        // Organization 2: Contoso (isolation test org)
        // ------------------------------------------------------------------
        $contoso = Organization::create([
            'name' => 'Contoso Facilities',
            'slug' => 'contoso',
            'timezone' => 'UTC',
            'status' => 'active',
            'contact_email' => 'ops@contoso.test',
            'contact_phone' => '+1 555 0100',
            'address' => '500 Contoso Plaza, Redmond, WA, USA',
            'settings' => [],
        ]);

        $contosoAdmin = User::create([
            'name' => 'Contoso Administrator',
            'email' => 'admin@contoso.test',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $contoso->users()->attach($contosoAdmin->id, ['role' => 'organization_admin', 'is_active' => true]);

        // ------------------------------------------------------------------
        // Northwind hierarchy: Site -> Building -> Floor -> Spaces
        // ------------------------------------------------------------------
        $site = Site::create([
            'organization_id' => $northwind->id,
            'name' => 'GreenTech HQ',
            'code' => 'GT-HQ',
            'address' => 'Plot 12, Main Boulevard, Karachi',
            'timezone' => 'Asia/Karachi',
            'status' => 'active',
        ]);

        $building = Building::create([
            'organization_id' => $northwind->id,
            'site_id' => $site->id,
            'name' => 'Building A',
            'code' => 'GT-A',
            'status' => 'active',
        ]);

        $floor1 = Floor::create([
            'organization_id' => $northwind->id,
            'building_id' => $building->id,
            'name' => 'Floor 1',
            'floor_number' => 1,
            'status' => 'active',
        ]);

        $floor2 = Floor::create([
            'organization_id' => $northwind->id,
            'building_id' => $building->id,
            'name' => 'Floor 2',
            'floor_number' => 2,
            'status' => 'active',
        ]);

        $spaces = [];
        foreach ([
            ['floor' => $floor1, 'name' => 'Room 101', 'type' => 'room'],
            ['floor' => $floor1, 'name' => 'Room 102', 'type' => 'room'],
            ['floor' => $floor1, 'name' => 'Conference Room A', 'type' => 'hall'],
            ['floor' => $floor2, 'name' => 'Room 201', 'type' => 'room'],
            ['floor' => $floor2, 'name' => 'Room 202', 'type' => 'room'],
            ['floor' => $floor2, 'name' => 'Server Room', 'type' => 'room'],
            ['floor' => $floor2, 'name' => 'Executive Suite', 'type' => 'room'],
        ] as $spec) {
            $spaces[] = Space::create([
                'organization_id' => $northwind->id,
                'floor_id' => $spec['floor']->id,
                'parent_space_id' => null,
                'name' => $spec['name'],
                'code' => strtoupper(str_replace(' ', '-', $spec['name'])),
                'space_type' => $spec['type'],
                'status' => 'active',
                'area_sqm' => fake()->randomElement([12.5, 18.0, 24.0, 32.5, 45.0, 60.0]),
            ]);
        }

        // Nested space under Room 101 (tests recursive policy + tree)
        $subSpace = Space::create([
            'organization_id' => $northwind->id,
            'floor_id' => $floor1->id,
            'parent_space_id' => $spaces[0]->id,
            'name' => 'Room 101 Annex',
            'code' => 'ROOM-101-ANNEX',
            'space_type' => 'section',
            'status' => 'active',
            'area_sqm' => 8.0,
        ]);

        // ------------------------------------------------------------------
        // AC units + devices + state snapshots (visually rich mix)
        // ------------------------------------------------------------------
        $profileVersions = IrProfileVersion::where('validation_status', 'validated')->get();

        $acSpecs = [
            ['space' => $spaces[0], 'name' => 'Main Office AC',   'code' => 'AC-101-01', 'status' => 'active',  'power' => PowerState::KnownOn,  'setpoint' => 22],
            ['space' => $spaces[1], 'name' => 'Reception AC',     'code' => 'AC-102-01', 'status' => 'active',  'power' => PowerState::KnownOff, 'setpoint' => 24],
            ['space' => $spaces[2], 'name' => 'Conference AC',    'code' => 'AC-103-01', 'status' => 'active',  'power' => PowerState::KnownOn,  'setpoint' => 20],
            ['space' => $spaces[3], 'name' => 'Room 201 AC',      'code' => 'AC-201-01', 'status' => 'active',  'power' => PowerState::Unknown,  'setpoint' => null],
            ['space' => $spaces[4], 'name' => 'Room 202 AC',      'code' => 'AC-202-01', 'status' => 'active',  'power' => PowerState::KnownOff, 'setpoint' => 25],
            ['space' => $spaces[5], 'name' => 'Server Room AC',   'code' => 'AC-SRV-01', 'status' => 'active',  'power' => PowerState::KnownOn,  'setpoint' => 18],
            ['space' => $spaces[6], 'name' => 'Executive AC',     'code' => 'AC-EXE-01', 'status' => 'active',  'power' => PowerState::Stale,    'setpoint' => 21],
            ['space' => $subSpace,    'name' => 'Annex AC',       'code' => 'AC-ANX-01', 'status' => 'maintenance', 'power' => PowerState::KnownOff, 'setpoint' => 23],
        ];

        $acs = [];
        foreach ($acSpecs as $index => $spec) {
            $profile = $profileVersions->random();
            $model = $profile->profile->acModel;

            $ac = AcUnit::create([
                'organization_id' => $northwind->id,
                'space_id' => $spec['space']->id,
                'ac_model_id' => $model->id,
                'ir_profile_version_id' => $profile->id,
                'name' => $spec['name'],
                'asset_code' => $spec['code'],
                'serial_number' => 'SN-' . Str::upper(fake()->bothify('########')),
                'installation_date' => fake()->dateTimeBetween('-3 years', 'today')->format('Y-m-d'),
                'status' => $spec['status'],
                'notes' => 'Seeded demonstration unit for Variant 7 QA.',
            ]);

            $stale = $spec['power'] === PowerState::Stale;
            $observedAt = $stale
                ? now()->subHours(fake()->numberBetween(3, 24))
                : ($spec['power'] === PowerState::Unknown ? null : now()->subMinutes(fake()->numberBetween(1, 90)));

            AcStateSnapshot::create([
                'organization_id' => $northwind->id,
                'ac_unit_id' => $ac->id,
                'power_state' => $spec['power'],
                'temperature_setpoint_c' => $spec['setpoint'],
                'operating_mode' => 'cool',
                'fan_setting' => 'auto',
                'swing_setting' => 'auto',
                'source' => 'device',
                'observed_at' => $observedAt,
                'received_at' => $observedAt ? now()->parse($observedAt)->addSeconds(2) : null,
                'quality' => $stale ? StateQuality::Stale : StateQuality::Reported,
                'ir_profile_version_id' => $profile->id,
            ]);

            $acs[] = $ac;
        }

        // Devices: one per AC (mostly online), plus one unassigned offline device
        foreach ($acs as $index => $ac) {
            $online = $index < 6;
            $lastSeen = $online ? now()->subMinutes(fake()->numberBetween(1, 240)) : now()->subHours(fake()->numberBetween(6, 48));

            $device = Device::create([
                'organization_id' => $northwind->id,
                'device_uid' => 'ESP32-' . Str::upper(fake()->bothify('########')),
                'name' => 'ESP32-' . $ac->asset_code,
                'hardware_model' => 'ESP32-WROOM-32',
                'firmware_version' => '1.2.0',
                'status' => 'active',
                'api_token_hash' => hash('sha256', 'acv1_' . Str::random(60)),
                'token_prefix' => 'ESP32-' . strtoupper(fake()->bothify('####')),
                'last_seen_at' => $lastSeen,
                'offline_notified_at' => null,
                'last_ip' => fake()->ipv4(),
                'wifi_rssi_dbm' => fake()->numberBetween(-70, -40),
                'effective_remote_policy' => 'disabled',
                'policy_version' => 1,
                'policy_synced_at' => null,
                'local_config' => [],
            ]);

            DeviceAssignment::create([
                'organization_id' => $northwind->id,
                'device_id' => $device->id,
                'ac_unit_id' => $ac->id,
                'assigned_at' => now()->subDays(fake()->numberBetween(1, 30)),
                'unassigned_at' => null,
                'is_current' => true,
                'assigned_by' => $northwindAdmin->id,
            ]);
        }

        // Unassigned offline device (tests unknown connectivity)
        Device::create([
            'organization_id' => $northwind->id,
            'device_uid' => 'ESP32-' . Str::upper(fake()->bothify('########')),
            'name' => 'ESP32-Unassigned',
            'hardware_model' => 'ESP32-WROOM-32',
            'firmware_version' => '1.0.0',
            'status' => 'active',
            'api_token_hash' => hash('sha256', 'acv1_' . Str::random(60)),
            'token_prefix' => 'ESP32-UN',
            'last_seen_at' => now()->subDays(2),
            'offline_notified_at' => null,
            'last_ip' => fake()->ipv4(),
            'wifi_rssi_dbm' => -85,
            'effective_remote_policy' => 'disabled',
            'policy_version' => 1,
            'policy_synced_at' => null,
            'local_config' => [],
        ]);

        // ------------------------------------------------------------------
        // Remote policies: org baseline + one AC override
        // ------------------------------------------------------------------
        RemotePolicy::create([
            'organization_id' => $northwind->id,
            'scope_type' => PolicyScope::Ac->value,
            'scope_id' => $acs[2]->id, // Conference AC override to enabled
            'policy_value' => PolicyValue::Enabled,
            'version' => 1,
            'updated_by' => $northwindAdmin->id,
        ]);

        // ------------------------------------------------------------------
        // Commands (lifecycle facts)
        // ------------------------------------------------------------------
        $issuedAt = now()->subHours(2);
        $completedCommand = AcCommand::create([
            'id' => (string) Str::uuid(),
            'organization_id' => $northwind->id,
            'ac_unit_id' => $acs[0]->id,
            'device_id' => Device::where('organization_id', $northwind->id)->where('name', 'like', '%' . $acs[0]->asset_code . '%')->first()->id,
            'requested_by' => $northwindAdmin->id,
            'command_type' => 'set_state',
            'desired_state' => ['power' => 'off', 'temperature_setpoint_c' => 24],
            'ir_profile_version_id' => $acs[0]->ir_profile_version_id,
            'status' => CommandStatus::Completed,
            'issued_at' => $issuedAt,
            'expires_at' => $issuedAt->copy()->addSeconds(300),
            'published_at' => $issuedAt->copy()->addSeconds(1),
            'received_at' => $issuedAt->copy()->addSeconds(2),
            'transmission_at' => $issuedAt->copy()->addSeconds(3),
            'completed_at' => $issuedAt->copy()->addSeconds(4),
            'failure_code' => null,
            'failure_message' => null,
            'idempotency_key' => null,
            'correlation_id' => (string) Str::uuid(),
            'client_request_id' => null,
            'device_result' => ['transmitted' => true, 'ack_received' => true],
            'retry_count' => 0,
        ]);

        AcCommand::create([
            'id' => (string) Str::uuid(),
            'organization_id' => $northwind->id,
            'ac_unit_id' => $acs[1]->id,
            'device_id' => Device::where('organization_id', $northwind->id)->where('name', 'like', '%' . $acs[1]->asset_code . '%')->first()->id,
            'requested_by' => $northwindAdmin->id,
            'command_type' => 'set_state',
            'desired_state' => ['power' => 'on', 'temperature_setpoint_c' => 22],
            'ir_profile_version_id' => $acs[1]->ir_profile_version_id,
            'status' => CommandStatus::Created,
            'issued_at' => now()->subMinutes(30),
            'expires_at' => now()->subMinutes(30)->copy()->addSeconds(300),
            'published_at' => null,
            'received_at' => null,
            'transmission_at' => null,
            'completed_at' => null,
            'failure_code' => null,
            'failure_message' => null,
            'idempotency_key' => null,
            'correlation_id' => (string) Str::uuid(),
            'client_request_id' => null,
            'device_result' => null,
            'retry_count' => 1,
        ]);

        // ------------------------------------------------------------------
        // Device events + state history (append-only facts)
        // ------------------------------------------------------------------
        foreach ($acs as $ac) {
            $device = $ac->device();
            if (! $device) continue;

            // Heartbeat
            $hbAt = now()->subMinutes(fake()->numberBetween(5, 100));
            DeviceEvent::create([
                'id' => (string) Str::uuid(),
                'event_uid' => (string) Str::uuid(),
                'organization_id' => $northwind->id,
                'device_id' => $device->id,
                'ac_unit_id' => $ac->id,
                'command_id' => null,
                'event_type' => 'heartbeat',
                'source' => EventSource::Device,
                'classification' => 'telemetry',
                'device_occurred_at' => $hbAt,
                'server_received_at' => now()->parse($hbAt)->addSeconds(1),
                'schema_version' => 1,
                'payload' => ['rssi' => fake()->numberBetween(-70, -40), 'simulated' => true],
                'processing_status' => 'processed',
            ]);

            // State report
            $reportAt = now()->subMinutes(fake()->numberBetween(10, 200));
            DeviceEvent::create([
                'id' => (string) Str::uuid(),
                'event_uid' => (string) Str::uuid(),
                'organization_id' => $northwind->id,
                'device_id' => $device->id,
                'ac_unit_id' => $ac->id,
                'command_id' => null,
                'event_type' => 'state.report',
                'source' => EventSource::Device,
                'classification' => 'telemetry',
                'device_occurred_at' => $reportAt,
                'server_received_at' => now()->parse($reportAt)->addSeconds(1),
                'schema_version' => 1,
                'payload' => ['power' => 'on', 'temperature_setpoint_c' => fake()->numberBetween(18, 26), 'simulated' => true],
                'processing_status' => 'processed',
            ]);

            AcStateHistory::create([
                'organization_id' => $northwind->id,
                'ac_unit_id' => $ac->id,
                'device_event_id' => null,
                'command_id' => null,
                'source' => 'device',
                'state_field' => 'power_state',
                'old_value' => ['power' => 'off'],
                'new_value' => ['power' => 'on'],
                'observed_at' => $reportAt,
                'received_at' => now()->parse($reportAt)->addSeconds(1),
                'quality' => StateQuality::Reported,
            ]);

            // Physical remote signal
            $remoteAt = now()->subMinutes(fake()->numberBetween(60, 500));
            DeviceEvent::create([
                'id' => (string) Str::uuid(),
                'event_uid' => (string) Str::uuid(),
                'organization_id' => $northwind->id,
                'device_id' => $device->id,
                'ac_unit_id' => $ac->id,
                'command_id' => null,
                'event_type' => 'remote.signal',
                'source' => EventSource::PhysicalRemote,
                'classification' => 'physical_remote',
                'device_occurred_at' => $remoteAt,
                'server_received_at' => now()->parse($remoteAt)->addSeconds(1),
                'schema_version' => 1,
                'payload' => ['decoded' => true, 'command' => 'power_toggle', 'simulated' => true],
                'processing_status' => 'processed',
            ]);

            // Activity log entries
            ActivityLog::create([
                'organization_id' => $northwind->id,
                'ac_unit_id' => $ac->id,
                'source' => EventSource::Portal,
                'action' => 'command_issued',
                'description' => 'Power command issued from portal for ' . $ac->asset_code . '.',
                'result' => 'success',
                'user_id' => $northwindAdmin->id,
                'device_id' => $device->id,
                'command_id' => null,
                'device_event_id' => null,
                'occurred_at' => now()->subMinutes(fake()->numberBetween(5, 200)),
                'metadata' => [],
            ]);
        }

        // ------------------------------------------------------------------
        // Notifications
        // ------------------------------------------------------------------
        Notification::create([
            'organization_id' => $northwind->id,
            'user_id' => $northwindAdmin->id,
            'type' => 'device_offline',
            'severity' => NotificationSeverity::Warning,
            'title' => 'Device offline',
            'message' => 'ESP32-Server Room has not reported for over 2 hours.',
            'ac_unit_id' => $acs[5]->id,
            'device_id' => Device::where('organization_id', $northwind->id)->where('name', 'like', '%SRV%')->first()->id,
            'device_event_id' => null,
            'read_at' => null,
            'action_url' => route('acs.show', $acs[5]),
            'occurred_at' => now()->subHours(3),
        ]);

        Notification::create([
            'organization_id' => $northwind->id,
            'user_id' => $northwindAdmin->id,
            'type' => 'command_failure',
            'severity' => NotificationSeverity::Alert,
            'title' => 'Command delivery failed',
            'message' => 'Power OFF command for AC-102-01 expired before device acknowledgement.',
            'ac_unit_id' => $acs[1]->id,
            'device_id' => Device::where('organization_id', $northwind->id)->where('name', 'like', '%102%')->first()->id,
            'device_event_id' => null,
            'read_at' => now(),
            'action_url' => route('acs.show', $acs[1]),
            'occurred_at' => now()->subHours(5),
        ]);

        Notification::create([
            'organization_id' => $northwind->id,
            'user_id' => $northwindAdmin->id,
            'type' => 'physical_remote',
            'severity' => NotificationSeverity::Info,
            'title' => 'Physical remote activity',
            'message' => 'A physical remote signal was detected on Conference AC (AC-103-01).',
            'ac_unit_id' => $acs[2]->id,
            'device_id' => Device::where('organization_id', $northwind->id)->where('name', 'like', '%103%')->first()->id,
            'device_event_id' => null,
            'read_at' => null,
            'action_url' => route('acs.show', $acs[2]),
            'occurred_at' => now()->subHours(1),
        ]);

        // ------------------------------------------------------------------
        // Daily usage aggregates (today + recent days)
        // ------------------------------------------------------------------
        $today = now($northwind->timezone)->startOfDay();
        foreach ($acs as $ac) {
            $onSeconds = $ac->stateSnapshot?->power_state === PowerState::KnownOn
                ? fake()->numberBetween(7200, 28800)
                : fake()->randomElement([0, 1800, 3600]);

            AcDailyUsage::create([
                'organization_id' => $northwind->id,
                'ac_unit_id' => $ac->id,
                'usage_date' => $today,
                'on_seconds' => $onSeconds,
                'unknown_seconds' => $ac->stateSnapshot?->power_state === PowerState::Unknown ? 3600 : 0,
                'incomplete' => $ac->stateSnapshot?->power_state !== PowerState::KnownOn && $ac->stateSnapshot?->power_state !== PowerState::KnownOff,
                'calculated_at' => now(),
            ]);
        }

        // ------------------------------------------------------------------
        // Generated reports (one completed PDF, one CSV)
        // ------------------------------------------------------------------
        GeneratedReport::create([
            'id' => (string) Str::uuid(),
            'organization_id' => $northwind->id,
            'requested_by' => $northwindAdmin->id,
            'report_type' => 'daily_usage',
            'title' => 'Daily Usage Report',
            'filters' => ['from' => $today->copy()->subDays(6)->toDateString(), 'to' => $today->toDateString()],
            'format' => 'pdf',
            'status' => 'completed',
            'file_path' => 'reports/' . $northwind->id . '/' . Str::uuid() . '.pdf',
            'error_message' => null,
            'completed_at' => now()->subHours(6),
            'expires_at' => now()->addDays(30),
        ]);

        GeneratedReport::create([
            'id' => (string) Str::uuid(),
            'organization_id' => $northwind->id,
            'requested_by' => $northwindAdmin->id,
            'report_type' => 'ac_history',
            'title' => 'AC ON/OFF History Report',
            'filters' => ['from' => $today->copy()->subDays(30)->toDateString(), 'to' => $today->toDateString()],
            'format' => 'csv',
            'status' => 'completed',
            'file_path' => 'reports/' . $northwind->id . '/' . Str::uuid() . '.csv',
            'error_message' => null,
            'completed_at' => now()->subDays(1),
            'expires_at' => now()->addDays(30),
        ]);
    }
}
