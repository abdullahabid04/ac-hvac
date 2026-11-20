<?php

namespace App\Services;

use App\Enums\EventSource;
use App\Models\AcCommand;
use App\Models\AcUnit;
use App\Models\Device;
use App\Models\DeviceEvent;
use App\Models\Organization;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;

class ActivityService
{
    public function record(
        Organization|int $organization,
        ?AcUnit $acUnit,
        EventSource|string $source,
        string $action,
        string $description,
        ?string $result = 'info',
        ?User $user = null,
        ?Device $device = null,
        ?AcCommand $command = null,
        ?DeviceEvent $deviceEvent = null,
        ?CarbonInterface $occurredAt = null,
        array $metadata = [],
    ): \App\Models\ActivityLog {
        $organizationId = $organization instanceof Organization ? $organization->id : $organization;
        $sourceValue = $source instanceof EventSource ? $source->value : $source;

        return \App\Models\ActivityLog::create([
            'organization_id' => $organizationId,
            'ac_unit_id' => $acUnit?->id,
            'source' => $sourceValue,
            'action' => $action,
            'description' => $description,
            'result' => $result,
            'user_id' => $user?->id,
            'device_id' => $device?->id,
            'command_id' => $command?->id,
            'device_event_id' => $deviceEvent?->id,
            'occurred_at' => $occurredAt ? Carbon::instance($occurredAt) : now(),
            'metadata' => $metadata ?: null,
        ]);
    }
}
