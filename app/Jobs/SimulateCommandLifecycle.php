<?php

namespace App\Jobs;

use App\Services\SimulatorService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

/**
 * Development-only job. It emits explicitly labelled simulated device events
 * through the same ingestion pipeline used by real devices.
 */
class SimulateCommandLifecycle implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $commandId) {}

    public function handle(SimulatorService $simulator): void
    {
        if (! config('hvac.simulator.auto_respond') || ! app()->environment('local')) {
            return;
        }

        $simulator->completeCommand($this->commandId);
    }
}
