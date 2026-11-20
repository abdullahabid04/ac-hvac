<?php

namespace App\Services;

use App\Enums\CommandStatus;
use App\Enums\EventSource;
use App\Enums\NotificationSeverity;
use App\Jobs\PublishAcCommand;
use App\Jobs\SimulateCommandLifecycle;
use App\Models\AcCommand;
use App\Models\AcUnit;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class AcCommandService
{
    public function __construct(
        private AcCapabilityValidator $validator,
        private ActivityService $activity,
        private NotificationService $notifications,
    ) {}

    public function create(Organization $organization, AcUnit $ac, array $data, User $user): AcCommand
    {
        if ($ac->organization_id !== $organization->id) {
            abort(404);
        }

        $idempotencyKey = $data['idempotency_key'] ?? $data['client_request_id'] ?? null;

        if ($idempotencyKey) {
            $existing = AcCommand::where('organization_id', $organization->id)
                ->where('idempotency_key', $idempotencyKey)
                ->first();

            if ($existing) {
                return $existing;
            }
        }

        $desiredState = $data['desired_state'] ?? [];

        if (! is_array($desiredState) || $desiredState === []) {
            abort(422, 'A desired_state object is required.');
        }

        $normalized = $this->validator->validateDesiredState($ac, $desiredState);
        $device = $ac->device();

        $command = DB::transaction(function () use ($organization, $ac, $device, $normalized, $user, $idempotencyKey, $data) {
            $command = AcCommand::create([
                'organization_id' => $organization->id,
                'ac_unit_id' => $ac->id,
                'device_id' => $device?->id,
                'requested_by' => $user->id,
                'command_type' => $data['action'] ?? 'set_state',
                'desired_state' => $normalized,
                'ir_profile_version_id' => $ac->ir_profile_version_id,
                'status' => CommandStatus::Created,
                'issued_at' => now(),
                'expires_at' => now()->addSeconds((int) config('hvac.command_ttl_seconds', 300)),
                'idempotency_key' => $idempotencyKey,
                'correlation_id' => (string) Str::uuid(),
                'client_request_id' => $data['client_request_id'] ?? null,
            ]);

            $this->activity->record(
                $organization,
                $ac,
                EventSource::Portal,
                'command_requested',
                "{$user->name} requested ".$this->describeState($normalized)." for {$ac->asset_code}.",
                'pending',
                user: $user,
                device: $device,
                command: $command,
            );

            return $command;
        });

        PublishAcCommand::dispatch($command->id);

        if (config('hvac.simulator.auto_respond') && app()->environment('local')) {
            SimulateCommandLifecycle::dispatchAfterResponse($command->id);
        }

        return $command->fresh();
    }

    public function expireStaleCommands(): int
    {
        $commands = AcCommand::query()
            ->whereIn('status', [
                CommandStatus::Created->value,
                CommandStatus::Published->value,
                CommandStatus::Received->value,
                CommandStatus::TransmissionAttempted->value,
            ])
            ->where('expires_at', '<', now())
            ->with(['organization', 'acUnit', 'device'])
            ->get();

        foreach ($commands as $command) {
            $command->update([
                'status' => CommandStatus::Expired,
                'failure_code' => 'expired',
                'failure_message' => 'The command expired before the device completed IR transmission.',
            ]);

            if ($command->acUnit) {
                $this->notifications->notify(
                    $command->organization,
                    'command_failure',
                    NotificationSeverity::Warning,
                    'Command expired',
                    "A command for {$command->acUnit->name} expired before completion. The AC state was not updated.",
                    $command->acUnit,
                    $command->device,
                    null,
                );
            }

            $this->activity->record(
                $command->organization,
                $command->acUnit,
                EventSource::System,
                'command_expired',
                "Command {$command->id} expired without device completion.",
                'warning',
                device: $command->device,
                command: $command,
            );
        }

        return $commands->count();
    }

    private function describeState(array $state): string
    {
        if (isset($state['power']) && count($state) === 1) {
            return 'power '.strtoupper($state['power']);
        }

        if (isset($state['power'], $state['temperature_setpoint_c'])) {
            return sprintf('%s at %d°C', strtoupper($state['power']), $state['temperature_setpoint_c']);
        }

        if (isset($state['temperature_setpoint_c'])) {
            return "setpoint {$state['temperature_setpoint_c']}°C";
        }

        return 'a state update';
    }
}
