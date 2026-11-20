<?php

use App\Models\AcCommand;
use App\Models\Device;
use App\Models\Organization;
use App\Services\AcCommandService;
use App\Services\DeviceHealthService;
use App\Services\SimulatorService;
use App\Services\UsageAggregationService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('hvac:expire-commands', function (AcCommandService $commands) {
    $count = $commands->expireStaleCommands();
    $this->info("Expired {$count} stale command(s).");
})->purpose('Expire device commands that passed their delivery window');

Artisan::command('hvac:detect-offline', function (DeviceHealthService $health) {
    $count = $health->detectOfflineDevices();
    $this->info("Detected {$count} newly offline device(s).");
})->purpose('Flag devices that stopped reporting heartbeats');

Artisan::command('hvac:rebuild-usage {--days=2}', function (UsageAggregationService $usage) {
    $days = max(1, (int) $this->option('days'));

    foreach (Organization::query()->cursor() as $organization) {
        $today = now($organization->timezone ?: 'UTC')->startOfDay();
        $usage->rebuildRange($organization, $today->copy()->subDays($days - 1), $today);
        $this->line("Rebuilt {$days} day(s) for {$organization->name}.");
    }
})->purpose('Rebuild ON-duration aggregates from state history');

if (app()->environment('local')) {
    Artisan::command('hvac:simulate-heartbeat {device}', function (SimulatorService $simulator) {
        $device = Device::where('device_uid', $this->argument('device'))->orWhere('id', $this->argument('device'))->firstOrFail();
        $simulator->heartbeat($device);
        $this->info("Simulated heartbeat for {$device->name}.");
    })->purpose('Development simulator: emit a heartbeat');

    Artisan::command('hvac:simulate-remote {device} {--off} {--setpoint=}', function (SimulatorService $simulator) {
        $device = Device::where('device_uid', $this->argument('device'))->orWhere('id', $this->argument('device'))->firstOrFail();
        $state = ['power' => $this->option('off') ? 'off' : 'on'];

        if ($this->option('setpoint') !== null) {
            $state['temperature_setpoint_c'] = (int) $this->option('setpoint');
        }

        $simulator->remoteAction($device, $state, true);
        $this->info("Simulated physical remote action for {$device->name}.");
    })->purpose('Development simulator: emit a physical remote event');

    Artisan::command('hvac:simulate-unknown {device}', function (SimulatorService $simulator) {
        $device = Device::where('device_uid', $this->argument('device'))->orWhere('id', $this->argument('device'))->firstOrFail();
        $simulator->unknownSignal($device);
        $this->info("Simulated unknown IR signal for {$device->name}.");
    })->purpose('Development simulator: emit an unknown IR signal');

    Artisan::command('hvac:simulate-state {device} {power} {--setpoint=}', function (SimulatorService $simulator) {
        $device = Device::where('device_uid', $this->argument('device'))->orWhere('id', $this->argument('device'))->firstOrFail();
        $state = ['power' => $this->argument('power')];

        if ($this->option('setpoint') !== null) {
            $state['temperature_setpoint_c'] = (int) $this->option('setpoint');
        }

        $simulator->stateReport($device, $state);
        $this->info("Simulated state report for {$device->name}.");
    })->purpose('Development simulator: emit a device state report');

    Artisan::command('hvac:simulate-command {command}', function (SimulatorService $simulator) {
        $simulator->completeCommand($this->argument('command'));
        $this->info('Simulated device command lifecycle.');
    })->purpose('Development simulator: acknowledge and transmit a queued command');

    Artisan::command('hvac:simulate-offline {device}', function () {
        $device = Device::where('device_uid', $this->argument('device'))->orWhere('id', $this->argument('device'))->firstOrFail();
        $device->update(['last_seen_at' => now()->subHours(3), 'offline_notified_at' => null]);
        $this->info("Marked {$device->name} as stale. Run hvac:detect-offline to raise the notification.");
    })->purpose('Development simulator: simulate a device going offline');
}

Schedule::command('hvac:expire-commands')->everyMinute()->withoutOverlapping();
Schedule::command('hvac:detect-offline')->everyMinute()->withoutOverlapping();
Schedule::command('hvac:rebuild-usage --days=2')->everyTenMinutes()->withoutOverlapping();
