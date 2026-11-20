<?php

namespace Tests\Feature;

use App\Models\AcCommand;
use App\Models\AcModel;
use App\Models\AcUnit;
use App\Models\Device;
use App\Models\IrProfile;
use App\Models\IrProfileVersion;
use App\Models\Organization;
use App\Models\Space;
use App\Models\User;
use App\Enums\PowerState;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AcTest extends TestCase
{
    use RefreshDatabase;

    public function test_ac_inventory_renders(): void
    {
        $user = $this->createUser();
        $orgId = $user->currentOrganization()->id;
        $space = Space::factory()->create(['organization_id' => $orgId]);
        AcUnit::factory()->count(3)->create(['organization_id' => $orgId, 'space_id' => $space->id]);

        $response = $this->actingAs($user)
            ->get('/air-conditioners');

        $response->assertStatus(200);
        $response->assertSeeText('3 air conditioners');
    }

    public function test_ac_details_render_with_tabs(): void
    {
        $user = $this->createUser();
        $orgId = $user->currentOrganization()->id;
        $space = Space::factory()->create(['organization_id' => $orgId]);
        $ac = AcUnit::factory()->create(['organization_id' => $orgId, 'space_id' => $space->id]);

        $response = $this->actingAs($user)
            ->get('/air-conditioners/' . $ac->id);

        $response->assertStatus(200);
        $response->assertSee($ac->asset_code);
        $response->assertSee('Overview');
        $response->assertSee('History');
        $response->assertSee('Usage');
        $response->assertSee('Settings');
    }

    public function test_create_ac(): void
    {
        $user = $this->createUser();
        $orgId = $user->currentOrganization()->id;
        $space = Space::factory()->create(['organization_id' => $orgId]);
        $model = AcModel::factory()->create();
        $profile = IrProfile::factory()->create(['ac_model_id' => $model->id]);
        $profileVersion = IrProfileVersion::factory()->create([
            'ir_profile_id' => $profile->id,
            'validation_status' => 'validated',
        ]);

        $response = $this->actingAs($user)
            ->post('/air-conditioners', [
                'name' => 'Test AC',
                'asset_code' => 'AC-TEST-01',
                'space_id' => $space->id,
                'ac_model_id' => $model->id,
                'ir_profile_version_id' => $profileVersion->id,
                'status' => 'active',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('ac_units', [
            'asset_code' => 'AC-TEST-01',
            'name' => 'Test AC',
            'organization_id' => $orgId,
        ]);
    }

    public function test_ac_control_requires_validated_profile_and_device(): void
    {
        $user = $this->createUser();
        $orgId = $user->currentOrganization()->id;
        $space = Space::factory()->create(['organization_id' => $orgId]);
        $ac = AcUnit::factory()->create([
            'organization_id' => $orgId,
            'space_id' => $space->id,
            'ir_profile_version_id' => IrProfileVersion::factory()->create([
                'validation_status' => 'unvalidated',
            ])->id,
        ]);

        $response = $this->actingAs($user)
            ->get('/air-conditioners/' . $ac->id . '/control');

        $response->assertStatus(200);
        $response->assertSee('Remote control is unavailable for this AC');
    }

    public function test_command_lifecycle(): void
    {
        $user = $this->createUser();
        $orgId = $user->currentOrganization()->id;
        $space = Space::factory()->create(['organization_id' => $orgId]);
        $profile = IrProfileVersion::factory()->create(['validation_status' => 'validated']);
        $model = $profile->profile->acModel;
        $ac = AcUnit::factory()->create([
            'organization_id' => $orgId,
            'ac_model_id' => $model->id,
            'ir_profile_version_id' => $profile->id,
            'space_id' => $space->id,
            'status' => 'active',
        ]);
        $device = Device::factory()->create(['organization_id' => $orgId]);
        \App\Models\DeviceAssignment::factory()->create([
            'organization_id' => $orgId,
            'device_id' => $device->id,
            'ac_unit_id' => $ac->id,
            'is_current' => true,
        ]);

        $response = $this->actingAs($user)
            ->withHeaders(['Accept' => 'application/json'])
            ->post('/air-conditioners/' . $ac->id . '/commands', [
                'desired_state' => ['power' => 'on', 'temperature_setpoint_c' => 22],
            ]);

        $response->assertStatus(201);
        $response->assertJsonStructure(['data' => ['id', 'status', 'is_pending', 'desired_state']]);

        $commandId = $response->json('data.id');

        $this->actingAs($user)
            ->get('/commands/' . $commandId)
            ->assertStatus(200);

        $this->assertDatabaseHas('ac_commands', ['id' => $commandId]);
    }

    public function test_invalid_setpoint_is_rejected(): void
    {
        $user = $this->createUser();
        $orgId = $user->currentOrganization()->id;
        $space = Space::factory()->create(['organization_id' => $orgId]);
        $profile = IrProfileVersion::factory()->create([
            'validation_status' => 'validated',
            'temperature_min_c' => 16,
            'temperature_max_c' => 30,
        ]);
        $model = $profile->profile->acModel;
        $ac = AcUnit::factory()->create([
            'organization_id' => $orgId,
            'ac_model_id' => $model->id,
            'ir_profile_version_id' => $profile->id,
            'space_id' => $space->id,
            'status' => 'active',
        ]);
        $device = Device::factory()->create(['organization_id' => $orgId]);
        \App\Models\DeviceAssignment::factory()->create([
            'organization_id' => $orgId,
            'device_id' => $device->id,
            'ac_unit_id' => $ac->id,
            'is_current' => true,
        ]);

        $response = $this->actingAs($user)
            ->withHeaders(['Accept' => 'application/json'])
            ->post('/air-conditioners/' . $ac->id . '/commands', [
                'desired_state' => ['power' => 'on', 'temperature_setpoint_c' => 99],
            ]);

        $response->assertStatus(422);
    }

    private function createUser(): User
    {
        $organization = Organization::factory()->create();
        $user = User::factory()->create(['password' => bcrypt('password')]);
        $organization->users()->attach($user->id, ['role' => 'organization_admin', 'is_active' => true]);

        return $user;
    }
}
