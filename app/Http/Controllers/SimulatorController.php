<?php

namespace App\Http\Controllers;

use App\Enums\PowerState;
use App\Models\AcCommand;
use App\Models\AcUnit;
use App\Models\Device;
use App\Services\SimulatorService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Development-only simulator console. Deliberately kept out of the manager
 * navigation and only registered when the local environment is active.
 */
class SimulatorController extends Controller
{
    public function __construct(private SimulatorService $simulator) {}

    public function index(Request $request)
    {
        $organization = $request->attributes->get('organization');

        return view('dev.simulator', [
            'organization' => $organization,
            'devices' => Device::where('organization_id', $organization->id)
                ->with(['currentAssignment.acUnit.space.floor.building.site', 'currentAssignment.acUnit.stateSnapshot'])
                ->orderBy('name')
                ->get(),
            'acs' => AcUnit::where('organization_id', $organization->id)->orderBy('name')->get(),
            'recentCommands' => AcCommand::where('organization_id', $organization->id)->orderByDesc('issued_at')->limit(10)->with('acUnit')->get(),
            'simulatorEnabled' => (bool) config('hvac.simulator.auto_respond'),
        ]);
    }

    public function action(Request $request)
    {
        abort_unless(app()->environment('local'), 404);

        $organization = $request->attributes->get('organization');
        $data = $request->validate([
            'action' => ['required', Rule::in(['heartbeat', 'state_report', 'remote_action', 'unknown_signal', 'complete_command', 'mark_offline', 'reconnect'])],
            'device_id' => ['required', 'integer'],
            'command_id' => ['nullable', 'string', 'max:40'],
            'power' => ['nullable', Rule::in(['on', 'off'])],
            'temperature_setpoint_c' => ['nullable', 'integer', 'between:10,35'],
        ]);

        $device = Device::where('organization_id', $organization->id)->findOrFail($data['device_id']);
        $ac = $device->acUnit();

        if (! $ac) {
            return back()->with('error', 'The selected device is not assigned to an air conditioner.');
        }

        switch ($data['action']) {
            case 'heartbeat':
            case 'reconnect':
                $this->simulator->heartbeat($device);
                break;

            case 'mark_offline':
                $device->update(['last_seen_at' => now()->subHours(3), 'offline_notified_at' => null]);
                break;

            case 'state_report':
                $state = array_filter([
                    'power' => $data['power'] ?? null,
                    'temperature_setpoint_c' => $data['temperature_setpoint_c'] ?? null,
                ], fn ($value) => $value !== null);
                $this->simulator->stateReport($device, $state);
                break;

            case 'remote_action':
                $state = array_filter([
                    'power' => $data['power'] ?? 'on',
                    'temperature_setpoint_c' => $data['temperature_setpoint_c'] ?? null,
                ], fn ($value) => $value !== null);
                $this->simulator->remoteAction($device, $state, true);
                break;

            case 'unknown_signal':
                $this->simulator->unknownSignal($device);
                break;

            case 'complete_command':
                $command = AcCommand::where('organization_id', $organization->id)
                    ->whereKey($data['command_id'] ?? '')
                    ->where('device_id', $device->id)
                    ->first();

                if (! $command) {
                    return back()->with('error', 'Select a pending command for this device.');
                }

                $this->simulator->completeCommand($command->id);
                break;
        }

        return back()->with('success', 'Simulator event emitted through the real device ingestion pipeline.');
    }
}
