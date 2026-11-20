<?php

namespace App\Http\Controllers\Api;

use App\Enums\CommandStatus;
use App\Models\AcCommand;
use App\Models\Device;
use App\Services\DeviceEventIngestionService;
use App\Services\RemotePolicyResolver;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DeviceController extends ApiController
{
    public function __construct(
        private DeviceEventIngestionService $ingestion,
        private RemotePolicyResolver $policies,
    ) {}

    public function heartbeat(Request $request)
    {
        $device = $this->device($request);
        $data = $request->validate([
            'schema_version' => ['required', 'integer', 'in:1'],
            'event_id' => ['required', 'string', 'max:80'],
            'occurred_at' => ['required', 'date'],
            'firmware_version' => ['nullable', 'string', 'max:40'],
            'wifi_rssi_dbm' => ['nullable', 'integer', 'between:-120,0'],
            'uptime_seconds' => ['nullable', 'integer', 'min:0'],
        ]);

        $result = $this->ingestion->ingest($device, array_merge($data, [
            'message_type' => 'device.heartbeat',
            'device_id' => $device->device_uid,
        ]), $request->ip());

        return $this->ok([
            'accepted' => true,
            'duplicate' => $result['duplicate'],
            'server_time' => now()->toIso8601String(),
            'policy' => [
                'effective' => $device->fresh()->effective_remote_policy,
                'version' => $device->fresh()->policy_version,
            ],
        ]);
    }

    public function config(Request $request)
    {
        $device = $this->device($request);
        $ac = $device->acUnit();

        if (! $ac) {
            return $this->error('NO_ASSIGNMENT', 'This device is not currently assigned to an air conditioner.', [], 409);
        }

        $ac->load(['irProfileVersion.profile.acModel', 'irProfileVersion.payloads', 'space.floor.building.site']);
        $effective = $this->policies->effective($ac);
        $profile = $ac->irProfileVersion;

        return $this->ok([
            'device' => [
                'uid' => $device->device_uid,
                'name' => $device->name,
                'firmware_version' => $device->firmware_version,
                'policy_version' => $device->policy_version,
            ],
            'ac' => [
                'id' => $ac->id,
                'asset_code' => $ac->asset_code,
                'name' => $ac->name,
                'model' => $ac->acModel?->displayName(),
                'location' => $ac->locationLabel(),
            ],
            'ir_profile' => $profile ? [
                'version_id' => $profile->id,
                'version' => $profile->version_number,
                'validated' => $profile->isValidated(),
                'temperature_min_c' => $profile->temperature_min_c,
                'temperature_max_c' => $profile->temperature_max_c,
                'temperature_step_c' => (float) $profile->temperature_step_c,
                'capabilities' => $profile->capabilities,
                'protocol' => $profile->protocol_metadata,
                'payloads' => $profile->payloads->map(fn ($payload) => [
                    'action_key' => $payload->action_key,
                    'representation_type' => $payload->representation_type,
                    'payload' => $payload->payload,
                    'carrier_frequency_hz' => $payload->carrier_frequency_hz,
                    'repeat_metadata' => $payload->repeat_metadata,
                ])->all(),
            ] : null,
            'remote_policy' => [
                'effective' => $effective['value']->value,
                'scope' => $effective['source_scope'],
                'version' => $effective['version'],
                'notice' => 'This policy cannot physically prevent a handheld remote IR beam from reaching the AC.',
            ],
            'pending_commands' => AcCommand::where('device_id', $device->id)
                ->whereIn('status', [CommandStatus::Published->value, CommandStatus::Received->value])
                ->where('expires_at', '>', now())
                ->count(),
            'server_time' => now()->toIso8601String(),
        ]);
    }

    public function pendingCommands(Request $request)
    {
        $device = $this->device($request);

        $commands = AcCommand::where('organization_id', $device->organization_id)
            ->where('device_id', $device->id)
            ->whereIn('status', [CommandStatus::Published->value, CommandStatus::Received->value])
            ->where('expires_at', '>', now())
            ->orderBy('issued_at')
            ->with('irProfileVersion.payloads')
            ->limit(20)
            ->get();

        return $this->ok($commands->map(fn (AcCommand $command) => [
            'id' => $command->id,
            'command_type' => $command->command_type,
            'desired_state' => $command->desired_state,
            'issued_at' => $command->issued_at?->toIso8601String(),
            'expires_at' => $command->expires_at?->toIso8601String(),
            'correlation_id' => $command->correlation_id,
            'profile_version_id' => $command->ir_profile_version_id,
            'payloads' => $command->irProfileVersion?->payloads->map(fn ($payload) => [
                'action_key' => $payload->action_key,
                'representation_type' => $payload->representation_type,
                'payload' => $payload->payload,
                'carrier_frequency_hz' => $payload->carrier_frequency_hz,
            ])->all() ?? [],
        ])->all(), [
            'poll_again_seconds' => 10,
            'delivery_note' => 'Acknowledgement and IR transmission result must be reported separately.',
        ]);
    }

    public function acknowledge(Request $request, AcCommand $command)
    {
        $device = $this->device($request);
        $data = $request->validate([
            'event_id' => ['required', 'string', 'max:80'],
            'occurred_at' => ['required', 'date'],
            'result' => ['required', Rule::in(['accepted', 'rejected', 'error', 'unsupported'])],
            'error_code' => ['nullable', 'string', 'max:60'],
            'error_message' => ['nullable', 'string', 'max:255'],
        ]);

        $result = $this->ingestion->ingest($device, array_merge($data, [
            'schema_version' => 1,
            'message_type' => 'command.ack',
            'device_id' => $device->device_uid,
            'command_id' => $command->id,
        ]), $request->ip());

        return $this->ok(['accepted' => true, 'duplicate' => $result['duplicate'], 'command' => $this->commandState($command)]);
    }

    public function transmissionResult(Request $request, AcCommand $command)
    {
        $device = $this->device($request);
        $data = $request->validate([
            'event_id' => ['required', 'string', 'max:80'],
            'occurred_at' => ['required', 'date'],
            'result' => ['required', Rule::in(['transmitted', 'failed', 'error'])],
            'error_code' => ['nullable', 'string', 'max:60'],
            'error_message' => ['nullable', 'string', 'max:255'],
            'raw_timings' => ['nullable', 'array'],
        ]);

        $result = $this->ingestion->ingest($device, array_merge($data, [
            'schema_version' => 1,
            'message_type' => 'command.transmission_result',
            'device_id' => $device->device_uid,
            'command_id' => $command->id,
        ]), $request->ip());

        return $this->ok([
            'accepted' => true,
            'duplicate' => $result['duplicate'],
            'command' => $this->commandState($command),
            'notice' => 'IR transmission success does not prove physical AC execution.',
        ]);
    }

    public function state(Request $request)
    {
        $device = $this->device($request);
        $data = $request->validate([
            'event_id' => ['required', 'string', 'max:80'],
            'occurred_at' => ['required', 'date'],
            'state' => ['required', 'array'],
            'state.power' => ['nullable', Rule::in(['on', 'off'])],
            'state.temperature_setpoint_c' => ['nullable', 'integer', 'between:5,40'],
            'state.operating_mode' => ['nullable', 'string', 'max:30'],
            'state.fan_setting' => ['nullable', 'string', 'max:30'],
            'state.swing_setting' => ['nullable', 'string', 'max:30'],
        ]);

        $result = $this->ingestion->ingest($device, array_merge($data, [
            'schema_version' => 1,
            'message_type' => 'state.report',
            'device_id' => $device->device_uid,
        ]), $request->ip());

        return $this->ok(['accepted' => true, 'duplicate' => $result['duplicate'], 'event_id' => $result['event']->id]);
    }

    public function remoteEvent(Request $request)
    {
        $device = $this->device($request);
        $data = $request->validate([
            'event_id' => ['required', 'string', 'max:80'],
            'occurred_at' => ['required', 'date'],
            'classification' => ['required', Rule::in(['recognized', 'unknown', 'decode_error'])],
            'policy_result' => ['required', Rule::in(['permitted', 'restricted', 'unknown'])],
            'recognized_action' => ['nullable', 'string', 'max:60'],
            'decoded_data' => ['nullable', 'array'],
            'raw_timings' => ['nullable', 'array'],
        ]);

        $result = $this->ingestion->ingest($device, array_merge($data, [
            'schema_version' => 1,
            'message_type' => 'remote.signal',
            'device_id' => $device->device_uid,
        ]), $request->ip());

        return $this->ok([
            'accepted' => true,
            'duplicate' => $result['duplicate'],
            'processing_status' => $result['event']->processing_status,
            'notice' => 'Detected remote activity is informational; software cannot block the direct IR beam.',
        ]);
    }

    public function event(Request $request)
    {
        $device = $this->device($request);
        $data = $request->validate([
            'schema_version' => ['required', 'integer', 'in:1'],
            'message_type' => ['required', Rule::in([
                'device.heartbeat', 'command.ack', 'command.transmission_result',
                'remote.signal', 'state.report', 'policy.ack',
            ])],
            'event_id' => ['required', 'string', 'max:80'],
            'occurred_at' => ['required', 'date'],
            'command_id' => ['nullable', 'uuid'],
        ]);

        $result = $this->ingestion->ingest($device, array_merge($request->all(), [
            'device_id' => $device->device_uid,
        ]), $request->ip());

        return $this->ok([
            'accepted' => true,
            'duplicate' => $result['duplicate'],
            'event_id' => $result['event']->id,
            'processing_status' => $result['event']->processing_status,
        ], [], $result['duplicate'] ? 200 : 202);
    }

    public function policyAck(Request $request)
    {
        $device = $this->device($request);
        $data = $request->validate([
            'event_id' => ['required', 'string', 'max:80'],
            'occurred_at' => ['required', 'date'],
            'effective_policy' => ['required', Rule::in(['enabled', 'disabled'])],
            'policy_version' => ['required', 'integer', 'min:1'],
        ]);

        $result = $this->ingestion->ingest($device, array_merge($data, [
            'schema_version' => 1,
            'message_type' => 'policy.ack',
            'device_id' => $device->device_uid,
        ]), $request->ip());

        return $this->ok(['accepted' => true, 'duplicate' => $result['duplicate'], 'policy_synced_at' => now()->toIso8601String()]);
    }

    private function device(Request $request): Device
    {
        return $request->attributes->get('device');
    }

    private function commandState(AcCommand $command): array
    {
        $command->refresh();

        return [
            'id' => $command->id,
            'status' => $command->status->value,
            'received_at' => $command->received_at?->toIso8601String(),
            'transmission_at' => $command->transmission_at?->toIso8601String(),
        ];
    }
}
