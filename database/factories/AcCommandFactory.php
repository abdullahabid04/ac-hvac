<?php

namespace Database\Factories;

use App\Models\AcCommand;
use App\Models\AcUnit;
use App\Models\Device;
use App\Models\Organization;
use App\Enums\CommandStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AcCommand>
 */
class AcCommandFactory extends Factory
{
    protected $model = AcCommand::class;

    public function definition(): array
    {
        $organization = Organization::factory()->create();
        $ac = AcUnit::factory()->create(['organization_id' => $organization->id]);
        $device = Device::factory()->create(['organization_id' => $organization->id]);
        $issuedAt = fake()->dateTimeBetween('-7 days', 'now');
        $status = fake()->randomElement([
            CommandStatus::Completed,
            CommandStatus::Completed,
            CommandStatus::Created,
            CommandStatus::Published,
            CommandStatus::Received,
            CommandStatus::TransmissionAttempted,
            CommandStatus::Failed,
            CommandStatus::Expired,
        ]);

        return [
            'id' => (string) Str::uuid(),
            'organization_id' => $organization->id,
            'ac_unit_id' => $ac->id,
            'device_id' => $device->id,
            'requested_by' => null,
            'command_type' => 'set_state',
            'desired_state' => [
                'power' => fake()->randomElement(['on', 'off']),
                'temperature_setpoint_c' => fake()->numberBetween(18, 26),
            ],
            'ir_profile_version_id' => $ac->ir_profile_version_id,
            'status' => $status,
            'issued_at' => $issuedAt,
            'expires_at' => now()->parse($issuedAt)->addSeconds(300),
            'published_at' => $status->isPending() ? now() : null,
            'received_at' => $status->value === 'received' || $status->isTerminal() ? now() : null,
            'transmission_at' => in_array($status->value, ['transmission_attempted', 'completed', 'failed']) ? now() : null,
            'completed_at' => $status === CommandStatus::Completed ? now() : null,
            'failure_code' => $status === CommandStatus::Failed ? 'DEVICE_TIMEOUT' : null,
            'failure_message' => $status === CommandStatus::Failed ? 'The device did not acknowledge the command before expiry.' : null,
            'idempotency_key' => null,
            'correlation_id' => (string) Str::uuid(),
            'client_request_id' => null,
            'device_result' => null,
            'retry_count' => fake()->numberBetween(0, 2),
        ];
    }
}
