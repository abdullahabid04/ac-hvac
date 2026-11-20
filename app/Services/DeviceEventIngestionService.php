<?php

namespace App\Services;

use App\Enums\CommandStatus;
use App\Enums\EventSource;
use App\Enums\NotificationSeverity;
use App\Enums\PowerState;
use App\Enums\StateQuality;
use App\Models\AcCommand;
use App\Models\Device;
use App\Models\DeviceEvent;
use Illuminate\Database\QueryException;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DeviceEventIngestionService
{
    private const MESSAGE_TYPES = [
        'device.heartbeat',
        'command.ack',
        'command.transmission_result',
        'remote.signal',
        'state.report',
        'policy.ack',
    ];

    public function __construct(
        private AcStateProjectionService $projection,
        private DeviceHealthService $deviceHealth,
        private ActivityService $activity,
        private NotificationService $notifications,
    ) {}

    /**
     * @return array{event: DeviceEvent, duplicate: bool}
     */
    public function ingest(Device $device, array $message, ?string $ip = null): array
    {
        $this->validateEnvelope($message, $device);

        $eventUid = (string) $message['event_id'];
        $existing = DeviceEvent::where('device_id', $device->id)->where('event_uid', $eventUid)->first();

        if ($existing) {
            return ['event' => $existing, 'duplicate' => true];
        }

        try {
            return DB::transaction(function () use ($device, $message, $ip, $eventUid) {
                $occurredAt = Carbon::parse($message['occurred_at'] ?? now())->utc();
                $command = null;

                if (! empty($message['command_id'])) {
                    $command = AcCommand::where('organization_id', $device->organization_id)
                        ->whereKey($message['command_id'])
                        ->where('device_id', $device->id)
                        ->first();

                    if (! $command) {
                        throw ValidationException::withMessages([
                            'command_id' => 'The command does not belong to this device.',
                        ]);
                    }
                }

                $ac = $command?->acUnit ?: $device->acUnit();

                $requiresAc = in_array($message['message_type'], [
                    'command.ack',
                    'command.transmission_result',
                    'remote.signal',
                    'state.report',
                ], true);

                if ($requiresAc && ! $ac) {
                    throw ValidationException::withMessages([
                        'device' => 'This device is not currently assigned to an air conditioner.',
                    ]);
                }

                $event = DeviceEvent::create([
                    'event_uid' => $eventUid,
                    'organization_id' => $device->organization_id,
                    'device_id' => $device->id,
                    'ac_unit_id' => $ac?->id,
                    'command_id' => $command?->id,
                    'event_type' => $message['message_type'],
                    'source' => $message['message_type'] === 'remote.signal'
                        ? EventSource::PhysicalRemote->value
                        : EventSource::Device->value,
                    'classification' => $message['classification'] ?? null,
                    'device_occurred_at' => $occurredAt,
                    'server_received_at' => now(),
                    'schema_version' => (int) ($message['schema_version'] ?? 1),
                    'payload' => $message,
                    'processing_status' => 'processing',
                ]);

                if ($ip) {
                    $device->update(['last_ip' => $ip]);
                }

                $processingStatus = match ($message['message_type']) {
                    'device.heartbeat' => $this->processHeartbeat($device, $message, $event, $occurredAt),
                    'command.ack' => $this->processCommandAck($device, $command, $message, $event, $occurredAt, $ac),
                    'command.transmission_result' => $this->processTransmissionResult($device, $command, $message, $event, $occurredAt, $ac),
                    'remote.signal' => $this->processRemoteSignal($device, $ac, $message, $event, $occurredAt),
                    'state.report' => $this->processStateReport($device, $ac, $message, $event, $occurredAt),
                    'policy.ack' => $this->processPolicyAck($device, $message, $event, $occurredAt),
                };

                $event->update(['processing_status' => $processingStatus]);

                return ['event' => $event->fresh(), 'duplicate' => false];
            });
        } catch (QueryException $exception) {
            if (str_contains($exception->getMessage(), 'device_events_device_uid_unique')
                || str_contains($exception->getMessage(), 'Duplicate entry')) {
                $existing = DeviceEvent::where('device_id', $device->id)->where('event_uid', $eventUid)->first();

                if ($existing) {
                    return ['event' => $existing, 'duplicate' => true];
                }
            }

            throw $exception;
        }
    }

    private function validateEnvelope(array $message, Device $device): void
    {
        if ((int) ($message['schema_version'] ?? 0) !== 1) {
            throw ValidationException::withMessages(['schema_version' => 'Unsupported device message schema version.']);
        }

        if (! in_array($message['message_type'] ?? null, self::MESSAGE_TYPES, true)) {
            throw ValidationException::withMessages(['message_type' => 'Unsupported device message type.']);
        }

        if (empty($message['event_id']) || ! is_string($message['event_id']) || strlen($message['event_id']) > 80) {
            throw ValidationException::withMessages(['event_id' => 'A stable event_id is required (max 80 characters).']);
        }

        if (! empty($message['device_id']) && $message['device_id'] !== $device->device_uid) {
            throw ValidationException::withMessages(['device_id' => 'Authenticated device identity does not match the payload.']);
        }

        if (empty($message['occurred_at'])) {
            throw ValidationException::withMessages(['occurred_at' => 'The device occurrence timestamp is required.']);
        }
    }

    private function processHeartbeat(Device $device, array $message, DeviceEvent $event, Carbon $occurredAt): string
    {
        $this->deviceHealth->recordHeartbeat($device, $message, $occurredAt);

        return 'processed';
    }

    private function processCommandAck(
        Device $device,
        ?AcCommand $command,
        array $message,
        DeviceEvent $event,
        Carbon $occurredAt,
        $ac,
    ): string {
        if (! $command) {
            throw ValidationException::withMessages(['command_id' => 'A command_id is required for command acknowledgements.']);
        }

        if ($command->expires_at->isPast() && ! $command->status->isTerminal()) {
            $command->update([
                'status' => CommandStatus::Expired,
                'failure_code' => 'expired_before_ack',
                'failure_message' => 'The command expired before the device acknowledged it.',
            ]);

            return 'rejected_expired';
        }

        $result = strtolower((string) ($message['result'] ?? 'accepted'));

        if (in_array($result, ['rejected', 'error', 'unsupported'], true)) {
            $command->update([
                'status' => CommandStatus::Failed,
                'failure_code' => $message['error_code'] ?? 'device_rejected',
                'failure_message' => $message['error_message'] ?? 'The device rejected the command.',
                'completed_at' => now(),
                'device_result' => $message,
            ]);

            $this->notifications->notify(
                $device->organization,
                'command_failure',
                NotificationSeverity::Alert,
                'Command rejected by device',
                "The ESP32 rejected a command for {$ac->name}. No AC state change is assumed.",
                $ac,
                $device,
                $event,
            );

            $this->activity->record(
                $device->organization,
                $ac,
                EventSource::Device,
                'command_rejected',
                "{$device->name} rejected the command for {$ac->asset_code}.",
                'failure',
                device: $device,
                command: $command,
                deviceEvent: $event,
                occurredAt: $occurredAt,
            );

            return 'processed';
        }

        $command->update([
            'status' => CommandStatus::Received,
            'received_at' => $occurredAt,
            'device_result' => $message,
        ]);

        $this->activity->record(
            $device->organization,
            $ac,
            EventSource::Device,
            'command_received',
            "{$device->name} acknowledged receipt of a command for {$ac->asset_code}.",
            'success',
            device: $device,
            command: $command,
            deviceEvent: $event,
            occurredAt: $occurredAt,
        );

        return 'processed';
    }

    private function processTransmissionResult(
        Device $device,
        ?AcCommand $command,
        array $message,
        DeviceEvent $event,
        Carbon $occurredAt,
        $ac,
    ): string {
        if (! $command) {
            throw ValidationException::withMessages(['command_id' => 'A command_id is required for transmission results.']);
        }

        if ($command->expires_at->isPast() && ! $command->status->isTerminal()) {
            $command->update([
                'status' => CommandStatus::Expired,
                'failure_code' => 'expired_before_transmission',
                'failure_message' => 'The command expired before the device reported transmission.',
                'device_result' => $message,
            ]);

            $this->activity->record(
                $device->organization,
                $ac,
                EventSource::Device,
                'command_expired',
                "A late transmission result arrived for an expired command on {$ac->asset_code}; state was not updated.",
                'warning',
                device: $device,
                command: $command,
                deviceEvent: $event,
                occurredAt: $occurredAt,
            );

            return 'rejected_expired';
        }

        $result = strtolower((string) ($message['result'] ?? 'transmitted'));

        if (in_array($result, ['failed', 'error'], true)) {
            $command->update([
                'status' => CommandStatus::Failed,
                'transmission_at' => $occurredAt,
                'completed_at' => now(),
                'failure_code' => $message['error_code'] ?? 'ir_transmission_failed',
                'failure_message' => $message['error_message'] ?? 'The device reported an IR transmission failure.',
                'device_result' => $message,
            ]);

            $this->notifications->notify(
                $device->organization,
                'command_failure',
                NotificationSeverity::Alert,
                'IR transmission failed',
                "The ESP32 reported an IR transmission failure for {$ac->name}. The AC may not have received the command.",
                $ac,
                $device,
                $event,
            );

            $this->activity->record(
                $device->organization,
                $ac,
                EventSource::Device,
                'transmission_failed',
                "IR transmission failed for {$ac->asset_code}.",
                'failure',
                device: $device,
                command: $command,
                deviceEvent: $event,
                occurredAt: $occurredAt,
            );

            return 'processed';
        }

        $command->update([
            'status' => CommandStatus::Completed,
            'transmission_at' => $occurredAt,
            'completed_at' => now(),
            'device_result' => $message,
        ]);

        $this->projection->applyConfiguration(
            $ac,
            $this->normalizeState($command->desired_state),
            'portal_command',
            StateQuality::Inferred,
            $occurredAt,
            now(),
            $event,
            $command,
        );

        $this->activity->record(
            $device->organization,
            $ac,
            EventSource::Device,
            'transmission_completed',
            "IR transmission completed for {$ac->asset_code}. This confirms transmission, not physical AC execution.",
            'success',
            device: $device,
            command: $command,
            deviceEvent: $event,
            occurredAt: $occurredAt,
        );

        $this->notifications->notify(
            $device->organization,
            'command_completed',
            NotificationSeverity::Info,
            'Command completed',
            "The ESP32 completed an IR transmission for {$ac->name}. The AC state is last-known/inferred, not physically confirmed.",
            $ac,
            $device,
            $event,
        );

        return 'processed';
    }

    private function processRemoteSignal(Device $device, $ac, array $message, DeviceEvent $event, Carbon $occurredAt): string
    {
        $classification = (string) ($message['classification'] ?? 'unknown');
        $policyResult = (string) ($message['policy_result'] ?? 'unknown');

        if ($classification === 'recognized' && $policyResult === 'permitted') {
            $state = $this->normalizeState($message['decoded_data'] ?? []);

            if ($state !== []) {
                $this->projection->applyConfiguration(
                    $ac,
                    $state,
                    'physical_remote',
                    StateQuality::Reported,
                    $occurredAt,
                    now(),
                    $event,
                );
            }

            $this->activity->record(
                $device->organization,
                $ac,
                EventSource::PhysicalRemote,
                'remote_recognized',
                "Physical remote action recognized and permitted on {$ac->asset_code}.",
                'success',
                device: $device,
                deviceEvent: $event,
                occurredAt: $occurredAt,
            );

            $this->notifications->notify(
                $device->organization,
                'physical_remote',
                NotificationSeverity::Info,
                'Physical remote activity',
                "A recognized physical remote action was reported for {$ac->name} and synchronized.",
                $ac,
                $device,
                $event,
            );

            return 'processed';
        }

        if ($classification === 'recognized' && $policyResult === 'restricted') {
            $this->activity->record(
                $device->organization,
                $ac,
                EventSource::PhysicalRemote,
                'remote_restricted',
                "A recognized physical remote signal was detected on {$ac->asset_code} but software policy restricted synchronization. The direct IR beam may still have reached the AC.",
                'warning',
                device: $device,
                command: null,
                deviceEvent: $event,
                occurredAt: $occurredAt,
            );

            $this->notifications->notify(
                $device->organization,
                'physical_remote',
                NotificationSeverity::Warning,
                'Restricted physical remote attempt detected',
                "A physical remote signal was captured for {$ac->name} but was not synchronized because of policy. Software cannot physically block the remote's direct IR beam.",
                $ac,
                $device,
                $event,
            );

            return 'processed';
        }

        $this->activity->record(
            $device->organization,
            $ac,
            EventSource::PhysicalRemote,
            $classification === 'decode_error' ? 'remote_decode_error' : 'remote_unknown',
            "An unknown or unsupported IR signal was captured on {$ac->asset_code}; no state change was assumed.",
            'warning',
            device: $device,
            deviceEvent: $event,
            occurredAt: $occurredAt,
        );

        $this->notifications->notify(
            $device->organization,
            'unknown_ir',
            NotificationSeverity::Warning,
            'Unknown IR signal captured',
            "An unknown or decode-error IR signal was reported for {$ac->name}. Last-known state was not changed.",
            $ac,
            $device,
            $event,
        );

        return 'processed';
    }

    private function processStateReport(Device $device, $ac, array $message, DeviceEvent $event, Carbon $occurredAt): string
    {
        $state = $this->normalizeState($message['state'] ?? []);

        if ($state === []) {
            return 'no_state_fields';
        }

        $changed = $this->projection->applyConfiguration(
            $ac,
            $state,
            'device_report',
            StateQuality::Reported,
            $occurredAt,
            now(),
            $event,
        );

        $this->activity->record(
            $device->organization,
            $ac,
            EventSource::Device,
            $changed ? 'state_reported' : 'state_report_received',
            $changed
                ? "{$device->name} reported a new known state for {$ac->asset_code}."
                : "{$device->name} reported state for {$ac->asset_code} without a newer change.",
            'success',
            device: $device,
            deviceEvent: $event,
            occurredAt: $occurredAt,
        );

        return $changed ? 'processed' : 'stored_out_of_order';
    }

    private function processPolicyAck(Device $device, array $message, DeviceEvent $event, Carbon $occurredAt): string
    {
        $device->update([
            'policy_synced_at' => $occurredAt,
            'effective_remote_policy' => $message['effective_policy'] ?? $device->effective_remote_policy,
            'policy_version' => (int) ($message['policy_version'] ?? $device->policy_version),
        ]);

        $this->activity->record(
            $device->organization,
            $device->acUnit(),
            EventSource::Device,
            'policy_synced',
            "{$device->name} acknowledged policy version {$device->policy_version}.",
            'success',
            device: $device,
            deviceEvent: $event,
            occurredAt: $occurredAt,
        );

        return 'processed';
    }

    /**
     * @return array<string, mixed>
     */
    public function normalizeState(array $state): array
    {
        $state = Arr::only($state, [
            'power',
            'temperature_setpoint_c',
            'setpoint',
            'temperature',
            'operating_mode',
            'mode',
            'fan_setting',
            'fan',
            'swing_setting',
            'swing',
        ]);

        $map = [
            'setpoint' => 'temperature_setpoint_c',
            'temperature' => 'temperature_setpoint_c',
            'mode' => 'operating_mode',
            'fan' => 'fan_setting',
            'swing' => 'swing_setting',
        ];

        foreach ($map as $from => $to) {
            if (array_key_exists($from, $state) && ! array_key_exists($to, $state)) {
                $state[$to] = $state[$from];
            }
            unset($state[$from]);
        }

        if (isset($state['power'])) {
            $power = strtolower((string) $state['power']);
            $state['power'] = match ($power) {
                'on', 'known_on', '1', 'true' => 'on',
                'off', 'known_off', '0', 'false' => 'off',
                default => null,
            };

            if ($state['power'] === null) {
                unset($state['power']);
            }
        }

        if (isset($state['temperature_setpoint_c']) && is_numeric($state['temperature_setpoint_c'])) {
            $state['temperature_setpoint_c'] = (int) $state['temperature_setpoint_c'];
        }

        return array_filter($state, fn ($value) => $value !== null && $value !== '');
    }
}
