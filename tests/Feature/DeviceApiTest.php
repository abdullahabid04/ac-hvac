<?php

namespace Tests\Feature;

use App\Models\Device;
use App\Models\DeviceAssignment;
use App\Models\Organization;
use App\Models\Space;
use App\Models\AcUnit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class DeviceApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_device_can_authenticate_with_serial_number(): void
    {
        $organization = Organization::factory()->create();
        $device = Device::factory()->create([
            'organization_id' => $organization->id,
            'status' => 'active',
            'serial_number' => 'HVAC-ESP32-2026-000001',
        ]);

        $response = $this->withHeader('X-Device-Serial', 'HVAC-ESP32-2026-000001')
            ->post('/api/v1/device/heartbeat', [
                'schema_version' => 1,
                'event_id' => 'hb-' . Str::uuid(),
                'occurred_at' => now()->toIso8601String(),
            ]);

        $response->assertStatus(200);
    }

    public function test_device_rejects_invalid_serial(): void
    {
        $response = $this->withHeader('X-Device-Serial', 'INVALID-SERIAL')
            ->post('/api/v1/device/heartbeat');

        $response->assertStatus(401);
        $response->assertJsonStructure(['error' => ['code', 'message']]);
    }

    public function test_device_rejects_missing_serial(): void
    {
        $response = $this->post('/api/v1/device/heartbeat');

        $response->assertStatus(401);
    }

    public function test_device_can_report_state(): void
    {
        $organization = Organization::factory()->create();
        $space = Space::factory()->create(['organization_id' => $organization->id]);
        $ac = AcUnit::factory()->create(['organization_id' => $organization->id, 'space_id' => $space->id]);
        $device = Device::factory()->create([
            'organization_id' => $organization->id,
            'status' => 'active',
            'serial_number' => 'HVAC-ESP32-2026-000001',
        ]);
        DeviceAssignment::factory()->create([
            'organization_id' => $organization->id,
            'device_id' => $device->id,
            'ac_unit_id' => $ac->id,
            'is_current' => true,
        ]);

        $response = $this->withHeader('X-Device-Serial', 'HVAC-ESP32-2026-000001')
            ->post('/api/v1/device/state', [
                'event_id' => 'st-' . Str::uuid(),
                'occurred_at' => now()->toIso8601String(),
                'state' => ['power' => 'on', 'temperature_setpoint_c' => 22],
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('device_events', [
            'device_id' => $device->id,
            'event_type' => 'state.report',
        ]);
    }

    public function test_event_ingestion_is_idempotent_by_event_uid(): void
    {
        $organization = Organization::factory()->create();
        $space = Space::factory()->create(['organization_id' => $organization->id]);
        $ac = AcUnit::factory()->create(['organization_id' => $organization->id, 'space_id' => $space->id]);
        $device = Device::factory()->create([
            'organization_id' => $organization->id,
            'status' => 'active',
            'serial_number' => 'HVAC-ESP32-2026-000001',
        ]);
        DeviceAssignment::factory()->create([
            'organization_id' => $organization->id,
            'device_id' => $device->id,
            'ac_unit_id' => $ac->id,
            'is_current' => true,
        ]);
        $eventUid = 'evt-' . Str::uuid();

        $payload = [
            'schema_version' => 1,
            'message_type' => 'device.heartbeat',
            'event_id' => $eventUid,
            'occurred_at' => now()->toIso8601String(),
            'payload' => [],
        ];

        $this->withHeader('X-Device-Serial', 'HVAC-ESP32-2026-000001')
            ->post('/api/v1/device/events', $payload);
        $this->withHeader('X-Device-Serial', 'HVAC-ESP32-2026-000001')
            ->post('/api/v1/device/events', $payload);

        $this->assertCount(1, $device->events()->where('event_uid', $eventUid)->get());
    }

    private function authHeader(string $token): array
    {
        return ['Authorization' => 'Bearer ' . $token];
    }
}
