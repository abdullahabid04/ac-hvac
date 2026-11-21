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
use App\Services\DeviceSerialService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class GiftUniversityDemoSeeder extends Seeder
{
    public function run(): void
    {
        // ------------------------------------------------------------------
        // 1. Create GIFT University organization
        // ------------------------------------------------------------------
        $gift = Organization::firstOrCreate(
            ['slug' => 'gift-university'],
            [
                'name' => 'GIFT University',
                'timezone' => 'Asia/Karachi',
                'status' => 'active',
                'contact_email' => 'facilities@gift.edu.pk',
                'contact_phone' => '+92 55 111 222 333',
                'address' => 'Gujranwala, Punjab, Pakistan',
                'settings' => [],
            ],
        );

        // ------------------------------------------------------------------
        // 2. Create demo administrator user
        // ------------------------------------------------------------------
        $demoAdmin = User::firstOrCreate(
            ['email' => 'demo.admin@gift.edu.pk'],
            [
                'name' => 'GIFT University Administrator',
                'password' => Hash::make(env('GIFT_DEMO_ADMIN_PASSWORD', 'password')),
                'email_verified_at' => now(),
                'job_title' => 'Organization Administrator',
                'phone' => '+92 55 111 222 333',
            ],
        );

        // Idempotent on repeated runs: the (organization_id, user_id) pivot
        // has a unique constraint, so guard the attach and refresh the role
        // when the membership already exists.
        if ($gift->users()->wherePivot('user_id', $demoAdmin->id)->exists()) {
            $gift->users()->updateExistingPivot($demoAdmin->id, [
                'role' => 'organization_admin',
                'is_active' => true,
            ]);
        } else {
            $gift->users()->attach($demoAdmin->id, [
                'role' => 'organization_admin',
                'is_active' => true,
            ]);
        }

        // Notification preferences for the demo user
        foreach (config('hvac.notification_categories') as $key => $label) {
            NotificationPreference::firstOrCreate(
                ['user_id' => $demoAdmin->id, 'key' => $key],
                [
                    'in_app' => true,
                    'email' => in_array($key, ['device_offline', 'command_failure'], true),
                ],
            );
        }

        // Organization-wide remote policy baseline (disabled/provisional)
        RemotePolicy::firstOrCreate(
            [
                'organization_id' => $gift->id,
                'scope_type' => PolicyScope::Organization->value,
                'scope_id' => $gift->id,
            ],
            [
                'policy_value' => PolicyValue::Disabled,
                'version' => 1,
                'updated_by' => $demoAdmin->id,
            ],
        );

        // ------------------------------------------------------------------
        // 3. Location hierarchy: Site -> Buildings -> Floors -> Spaces
        // ------------------------------------------------------------------
        $site = Site::firstOrCreate(
            [
                'organization_id' => $gift->id,
                'code' => 'GIFT-MC',
            ],
            [
                'name' => 'Main Campus',
                'address' => 'Gujranwala, Punjab, Pakistan',
                'timezone' => 'Asia/Karachi',
                'status' => 'active',
            ],
        );

        $academicBlock = Building::firstOrCreate(
            [
                'organization_id' => $gift->id,
                'site_id' => $site->id,
                'code' => 'ACAD-BLK',
            ],
            [
                'name' => 'Academic Block',
                'status' => 'active',
            ],
        );

        $groundFloor = Floor::firstOrCreate(
            [
                'organization_id' => $gift->id,
                'building_id' => $academicBlock->id,
                'floor_number' => 0,
            ],
            [
                'name' => 'Ground Floor',
                'status' => 'active',
            ],
        );

        $firstFloor = Floor::firstOrCreate(
            [
                'organization_id' => $gift->id,
                'building_id' => $academicBlock->id,
                'floor_number' => 1,
            ],
            [
                'name' => 'First Floor',
                'status' => 'active',
            ],
        );

        // Spaces on Ground Floor
        $lectureHall01 = Space::firstOrCreate(
            [
                'organization_id' => $gift->id,
                'floor_id' => $groundFloor->id,
                'parent_space_id' => null,
                'code' => 'LECTURE-HALL-01',
            ],
            [
                'name' => 'Lecture Hall 01',
                'space_type' => 'hall',
                'status' => 'active',
                'area_sqm' => 120.0,
            ],
        );

        $computerLab01 = Space::firstOrCreate(
            [
                'organization_id' => $gift->id,
                'floor_id' => $groundFloor->id,
                'parent_space_id' => null,
                'code' => 'COMPUTER-LAB-01',
            ],
            [
                'name' => 'Computer Lab 01',
                'space_type' => 'room',
                'status' => 'active',
                'area_sqm' => 60.0,
            ],
        );

        $facultyOffice = Space::firstOrCreate(
            [
                'organization_id' => $gift->id,
                'floor_id' => $groundFloor->id,
                'parent_space_id' => null,
                'code' => 'FACULTY-OFFICE',
            ],
            [
                'name' => 'Faculty Office',
                'space_type' => 'room',
                'status' => 'active',
                'area_sqm' => 40.0,
            ],
        );

        // Spaces on First Floor
        $conferenceRoom = Space::firstOrCreate(
            [
                'organization_id' => $gift->id,
                'floor_id' => $firstFloor->id,
                'parent_space_id' => null,
                'code' => 'CONFERENCE-ROOM',
            ],
            [
                'name' => 'Conference Room',
                'space_type' => 'hall',
                'status' => 'active',
                'area_sqm' => 80.0,
            ],
        );

        $room201 = Space::firstOrCreate(
            [
                'organization_id' => $gift->id,
                'floor_id' => $firstFloor->id,
                'parent_space_id' => null,
                'code' => 'ROOM-201',
            ],
            [
                'name' => 'Room 201',
                'space_type' => 'room',
                'status' => 'active',
                'area_sqm' => 40.0,
            ],
        );

        $room202 = Space::firstOrCreate(
            [
                'organization_id' => $gift->id,
                'floor_id' => $firstFloor->id,
                'parent_space_id' => null,
                'code' => 'ROOM-202',
            ],
            [
                'name' => 'Room 202',
                'space_type' => 'room',
                'status' => 'active',
                'area_sqm' => 35.0,
            ],
        );

        $serverRoom = Space::firstOrCreate(
            [
                'organization_id' => $gift->id,
                'floor_id' => $firstFloor->id,
                'parent_space_id' => null,
                'code' => 'SERVER-ROOM',
            ],
            [
                'name' => 'Server Room',
                'space_type' => 'room',
                'status' => 'active',
                'area_sqm' => 30.0,
            ],
        );

        $executiveSuite = Space::firstOrCreate(
            [
                'organization_id' => $gift->id,
                'floor_id' => $firstFloor->id,
                'parent_space_id' => null,
                'code' => 'EXECUTIVE-SUITE',
            ],
            [
                'name' => 'Executive Suite',
                'space_type' => 'room',
                'status' => 'active',
                'area_sqm' => 50.0,
            ],
        );

        // ------------------------------------------------------------------
        // 4. AC units
        // ------------------------------------------------------------------
        $profileVersions = IrProfileVersion::where('validation_status', 'validated')->get();

        // Allow this seeder to run standalone on a fresh database: seed a
        // minimal validated catalogue if none exists (idempotent via unique keys).
        if ($profileVersions->isEmpty()) {
            $model = AcModel::firstOrCreate(
                [
                    'manufacturer' => 'Daikin',
                    'model_name' => 'FTXM35TV16 Inverter',
                    'remote_variant' => 'inverter',
                ],
                ['protocol_name' => 'Daikin-NEC'],
            );

            $profile = IrProfile::firstOrCreate(
                ['ac_model_id' => $model->id, 'name' => 'Daikin FTXM35TV16 IR'],
                ['status' => 'active'],
            );

            IrProfileVersion::firstOrCreate(
                ['ir_profile_id' => $profile->id, 'version_number' => 2],
                [
                    'capabilities' => ['power' => true, 'setpoint' => true, 'mode' => true, 'fan' => true, 'swing' => true],
                    'temperature_min_c' => 16,
                    'temperature_max_c' => 32,
                    'temperature_step_c' => 0.5,
                    'validation_status' => 'validated',
                    'validation_notes' => 'Fallback validated profile for standalone demo seed.',
                    'validated_at' => now(),
                ],
            );

            $profileVersions = IrProfileVersion::where('validation_status', 'validated')->get();
        }

        $acSpecs = [
            [
                'space' => $lectureHall01,
                'name' => 'AC - Lecture Hall 01',
                'code' => 'AC-LECTURE-01',
                'status' => 'active',
                'power' => PowerState::KnownOn,
                'setpoint' => 22,
            ],
            [
                'space' => $computerLab01,
                'name' => 'AC - Computer Lab 01',
                'code' => 'AC-COMPUTER-01',
                'status' => 'active',
                'power' => PowerState::KnownOff,
                'setpoint' => 24,
            ],
            [
                'space' => $facultyOffice,
                'name' => 'AC - Faculty Office',
                'code' => 'AC-FACULTY-01',
                'status' => 'active',
                'power' => PowerState::KnownOff,
                'setpoint' => 23,
            ],
            [
                'space' => $conferenceRoom,
                'name' => 'AC - Conference Room',
                'code' => 'AC-CONFERENCE-01',
                'status' => 'active',
                'power' => PowerState::KnownOff,
                'setpoint' => 22,
            ],
        ];

        $acs = [];
        foreach ($acSpecs as $spec) {
            $profile = $profileVersions->random();
            $model = $profile->profile->acModel;

            $ac = AcUnit::firstOrCreate(
                [
                    'organization_id' => $gift->id,
                    'asset_code' => $spec['code'],
                ],
                [
                    'space_id' => $spec['space']->id,
                    'ac_model_id' => $model->id,
                    'ir_profile_version_id' => $profile->id,
                    'name' => $spec['name'],
                    'serial_number' => 'SN-' . Str::upper(fake()->bothify('########')),
                    'installation_date' => fake()->dateTimeBetween('-3 years', 'today')->format('Y-m-d'),
                    'status' => $spec['status'],
                    'notes' => 'Seeded demonstration unit for GIFT University QA.',
                ],
            );

            $stale = $spec['power'] === PowerState::Stale;
            $observedAt = $stale
                ? now()->subHours(fake()->numberBetween(3, 24))
                : ($spec['power'] === PowerState::Unknown ? null : now()->subMinutes(fake()->numberBetween(1, 90)));

            AcStateSnapshot::firstOrCreate(
                ['ac_unit_id' => $ac->id],
                [
                    'organization_id' => $gift->id,
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
                ],
            );

            $acs[] = $ac;
        }

        // ------------------------------------------------------------------
        // 5. Devices - one per AC
        // ------------------------------------------------------------------
        $devices = [];
        $serialService = new DeviceSerialService();
        foreach ($acs as $index => $ac) {
            $online = $index < 6;
            $lastSeen = $online ? now()->subMinutes(fake()->numberBetween(1, 240)) : now()->subHours(fake()->numberBetween(6, 48));

            $device = Device::firstOrCreate(
                [
                    'organization_id' => $gift->id,
                    // Deterministic per AC so repeated seed runs are idempotent
                    // (device_uid is unique and not a secret).
                    'device_uid' => 'ESP32-' . $ac->asset_code,
                ],
                [
                    'name' => 'ESP32-' . $ac->asset_code,
                    'hardware_model' => 'ESP32-WROOM-32',
                    'firmware_version' => '1.2.0',
                    'status' => 'active',
                    'api_token_hash' => hash('sha256', 'acv1_' . Str::random(60)),
                    'token_prefix' => 'ESP32-' . strtoupper(fake()->bothify('####')),
                    'last_seen_at' => $online ? now()->subMinutes(fake()->numberBetween(1, 240)) : now()->subHours(fake()->numberBetween(6, 48)),
                    'offline_notified_at' => null,
                    'last_ip' => fake()->ipv4(),
                    'wifi_rssi_dbm' => fake()->numberBetween(-70, -40),
                    'effective_remote_policy' => 'disabled',
                    'policy_version' => 1,
                    'policy_synced_at' => null,
                    'local_config' => [],
                ],
            );

            DeviceAssignment::firstOrCreate(
                [
                    'organization_id' => $gift->id,
                    'device_id' => $device->id,
                    'ac_unit_id' => $ac->id,
                    'is_current' => true,
                ],
                [
                    'assigned_at' => now()->subDays(fake()->numberBetween(1, 30)),
                    'assigned_by' => $demoAdmin->id,
                ],
            );

            // Assign a stable device serial number (demo simulator identifier).
            // Idempotent: only assign when the device has none yet.
            if (! $device->serial_number) {
                $serialService->assignSerialNumber($device);
            }

            $devices[] = $device;
        }

        // ------------------------------------------------------------------
        // 7. Notifications
        // ------------------------------------------------------------------
        $seedNotification = function (string $type, string $title, array $defaults) use ($gift, $demoAdmin, $acs, $devices): void {
            $exists = Notification::where('organization_id', $gift->id)
                ->where('user_id', $demoAdmin->id)
                ->where('type', $type)
                ->where('title', $title)
                ->exists();

            if (! $exists) {
                Notification::create(array_merge($defaults, [
                    'organization_id' => $gift->id,
                    'user_id' => $demoAdmin->id,
                    'type' => $type,
                    'title' => $title,
                ]));
            }
        };

        $seedNotification(
            'device_offline',
            'Device offline',
            [
                'severity' => NotificationSeverity::Warning,
                'message' => 'Device offline',
                'ac_unit_id' => $acs[0]->id,
                'device_id' => $devices[0]->id,
                'read_at' => null,
                'action_url' => route('acs.show', $acs[0]),
                'occurred_at' => now()->subHours(3),
            ],
        );

        $seedNotification(
            'command_failure',
            'Command delivery failed',
            [
                'severity' => NotificationSeverity::Alert,
                'message' => 'Power OFF command for AC-102-01 expired before device acknowledgement.',
                'ac_unit_id' => $acs[1]->id,
                'device_id' => $devices[1]->id,
                'device_event_id' => null,
                'read_at' => now(),
                'action_url' => route('acs.show', $acs[1]),
                'occurred_at' => now()->subHours(5),
            ],
        );

        $seedNotification(
            'physical_remote',
            'Physical remote activity',
            [
                'severity' => NotificationSeverity::Info,
                'message' => 'A physical remote signal was detected on Conference AC (AC-103-01).',
                'ac_unit_id' => $acs[2]->id,
                'device_id' => $devices[2]->id,
                'device_event_id' => null,
                'read_at' => null,
                'action_url' => route('acs.show', $acs[2]),
                'occurred_at' => now()->subHours(1),
            ],
        );

        // ------------------------------------------------------------------
        // 8. Daily usage aggregates
        // ------------------------------------------------------------------
        $today = now($gift->timezone)->startOfDay();
        foreach ($acs as $ac) {
            $onSeconds = $ac->stateSnapshot?->power_state === PowerState::KnownOn
                ? fake()->numberBetween(7200, 28800)
                : fake()->randomElement([0, 1800, 3600]);

            AcDailyUsage::firstOrCreate(
                [
                    'organization_id' => $gift->id,
                    'ac_unit_id' => $ac->id,
                    'usage_date' => $today,
                ],
                [
                    'on_seconds' => $onSeconds,
                    'unknown_seconds' => $ac->stateSnapshot?->power_state === PowerState::Unknown ? 3600 : 0,
                    'incomplete' => $ac->stateSnapshot?->power_state !== PowerState::KnownOn && $ac->stateSnapshot?->power_state !== PowerState::KnownOff,
                    'calculated_at' => now(),
                ],
            );
        }

        // ------------------------------------------------------------------
        // 9. Generated reports
        // ------------------------------------------------------------------
        $seedReport = function (string $reportType, string $title, array $filters, string $format) use ($gift, $demoAdmin, $today): void {
            $exists = GeneratedReport::where('organization_id', $gift->id)
                ->where('report_type', $reportType)
                ->where('title', $title)
                ->exists();

            if (! $exists) {
                GeneratedReport::create([
                    'organization_id' => $gift->id,
                    'requested_by' => $demoAdmin->id,
                    'report_type' => $reportType,
                    'title' => $title,
                    'filters' => $filters,
                    'format' => $format,
                    'status' => 'completed',
                    'file_path' => 'reports/' . $gift->id . '/' . Str::uuid() . '.' . $format,
                    'error_message' => null,
                    'completed_at' => $reportType === 'daily_usage' ? now()->subHours(6) : now()->subDays(1),
                    'expires_at' => now()->addDays(30),
                ]);
            }
        };

        $seedReport(
            'daily_usage',
            'Daily Usage Report',
            ['from' => $today->copy()->subDays(6)->toDateString(), 'to' => $today->toDateString()],
            'pdf',
        );

        $seedReport(
            'ac_history',
            'AC ON/OFF History Report',
            ['from' => $today->copy()->subDays(30)->toDateString(), 'to' => $today->toDateString()],
            'csv',
        );
    }
}
