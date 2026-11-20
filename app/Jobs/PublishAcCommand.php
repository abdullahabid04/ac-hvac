<?php

namespace App\Jobs;

use App\Models\AcCommand;
use App\Services\Contracts\DeviceMessagePublisher;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class PublishAcCommand implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $backoff = 5;

    public function __construct(public string $commandId) {}

    public function handle(DeviceMessagePublisher $publisher): void
    {
        $command = AcCommand::find($this->commandId);

        if (! $command || $command->status->isTerminal()) {
            return;
        }

        $publisher->publish($command);
    }
}
