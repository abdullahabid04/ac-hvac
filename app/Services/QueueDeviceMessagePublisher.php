<?php

namespace App\Services;

use App\Enums\CommandStatus;
use App\Enums\EventSource;
use App\Models\AcCommand;
use App\Services\Contracts\DeviceMessagePublisher;

/**
 * Development/reference transport: commands are queued in the database and
 * delivered when the authenticated device polls its pending command feed.
 * The interface is intentionally replaceable with an MQTT publisher.
 */
class QueueDeviceMessagePublisher implements DeviceMessagePublisher
{
    public function __construct(private ActivityService $activity) {}

    public function publish(AcCommand $command): bool
    {
        if ($command->expires_at->isPast()) {
            $command->update([
                'status' => CommandStatus::Expired,
                'failure_code' => 'expired_before_publish',
                'failure_message' => 'The command expired before it could be queued for delivery.',
            ]);

            return false;
        }

        if (! $command->device_id) {
            $command->update([
                'status' => CommandStatus::Failed,
                'failure_code' => 'no_device',
                'failure_message' => 'No device is assigned to the AC.',
            ]);

            return false;
        }

        $command->update([
            'status' => CommandStatus::Published,
            'published_at' => now(),
        ]);

        $this->activity->record(
            $command->organization,
            $command->acUnit,
            EventSource::System,
            'command_queued',
            "Command {$command->id} queued for device delivery.",
            'pending',
            user: $command->requestedBy,
            device: $command->device,
            command: $command,
        );

        return true;
    }
}
