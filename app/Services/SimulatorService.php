<?php

namespace App\Services;

use App\Enums\CommandStatus;
use App\Models\Device;
use Illuminate\Support\Str;

class SimulatorService
{
    public function __construct(private DeviceEventIngestionService $ingestion) {}

    public function completeCommand(string $commandId): void
    {
        $command = \App\Models\AcCommand::with(['device', 'acUnit'])->find($commandId);

        if (! $command || ! $command->device || $command->status->isTerminal()) {
            return;
        }

        $device = $command->device;

        $this->ingestion->ingest($device, [
            'schema_version' => 1,
            'message_type' => 'command.ack',
            'event_id' => 'sim-ack-'.$command->id,
            'device_id' => $device->device_uid,
            'command_id' => $command->id,
            'occurred_at' => now()->toIso8601String(),
            'result' => 'accepted',
            'simulated' => true,
        ]);

        $this->ingestion->ingest($device, [
            'schema_version' => 1,
            'message_type' => 'command.transmission_result',
            'event_id' => 'sim-tx-'.$command->id,
            'device_id' => $device->device_uid,
            'command_id' => $command->id,
            'occurred_at' => now()->toIso8601String(),
            'result' => 'transmitted',
            'simulated' => true,
        ]);
    }

    public function heartbeat(Device $device): void
    {
        $this->ingestion->ingest($device, [
            'schema_version' => 1,
            'message_type' => 'device.heartbeat',
            'event_id' => 'sim-heartbeat-'.Str::uuid(),
            'device_id' => $device->device_uid,
            'occurred_at' => now()->toIso8601String(),
            'firmware_version' => $device->firmware_version ?: 'sim-1.0.0',
            'uptime_seconds' => random_int(3600, 864000),
            'wifi_rssi_dbmm' => random_int(-85, -42),
            'simulated' => true,
        ]);
    }

    public function remoteAction(Device $device, array $state, bool $permitted = true): void
    {
        $this->ingestion->ingest($device, [
            'schema_version' => 1,
            'message_type' => 'remote.signal',
            'event_id' => 'sim-remote-'.Str::uuid(),
            'device_id' => $device->device_uid,
            'occurred_at' => now()->toIso8601String(),
            'classification' => 'recognized',
            'policy_result' => $permitted ? 'permitted' : 'restricted',
            'recognized_action' => 'set_state',
            'decoded_data' => $state,
            'simulated' => true,
        ]);
    }

    public function unknownSignal(Device $device): void
    {
        $this->ingestion->ingest($device, [
            'schema_version' => 1,
            'message_type' => 'remote.signal',
            'event_id' => 'sim-unknown-'.Str::uuid(),
            'device_id' => $device->device_uid,
            'occurred_at' => now()->toIso8601String(),
            'classification' => 'unknown',
            'policy_result' => 'permitted',
            'decoded_data' => [],
            'simulated' => true,
        ]);
    }

    public function stateReport(Device $device, array $state): void
    {
        $this->ingestion->ingest($device, [
            'schema_version' => 1,
            'message_type' => 'state.report',
            'event_id' => 'sim-state-'.Str::uuid(),
            'device_id' => $device->device_uid,
            'occurred_at' => now()->toIso8601String(),
            'state' => $state,
            'simulated' => true,
        ]);
    }
}
