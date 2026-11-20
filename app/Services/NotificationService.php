<?php

namespace App\Services;

use App\Enums\NotificationSeverity;
use App\Models\AcUnit;
use App\Models\Device;
use App\Models\DeviceEvent;
use App\Models\Notification;
use App\Models\NotificationPreference;
use App\Models\Organization;
use Illuminate\Support\Carbon;

class NotificationService
{
    public function notify(
        Organization $organization,
        string $type,
        NotificationSeverity $severity,
        string $title,
        string $message,
        ?AcUnit $acUnit = null,
        ?Device $device = null,
        ?DeviceEvent $deviceEvent = null,
        ?string $actionUrl = null,
        ?Carbon $occurredAt = null,
    ): void {
        $users = $organization->users()
            ->wherePivot('is_active', true)
            ->get();

        foreach ($users as $user) {
            $preference = NotificationPreference::query()
                ->where('user_id', $user->id)
                ->where('key', $type)
                ->first();

            if ($preference && ! $preference->in_app) {
                continue;
            }

            Notification::create([
                'organization_id' => $organization->id,
                'user_id' => $user->id,
                'type' => $type,
                'severity' => $severity,
                'title' => $title,
                'message' => $message,
                'ac_unit_id' => $acUnit?->id,
                'device_id' => $device?->id,
                'device_event_id' => $deviceEvent?->id,
                'action_url' => $actionUrl ?? ($acUnit ? route('acs.show', $acUnit) : null),
                'occurred_at' => $occurredAt ?? now(),
            ]);
        }
    }

    public function notifyAdmins(
        Organization $organization,
        string $type,
        NotificationSeverity $severity,
        string $title,
        string $message,
        ?AcUnit $acUnit = null,
        ?Device $device = null,
        ?DeviceEvent $deviceEvent = null,
        ?string $actionUrl = null,
        ?Carbon $occurredAt = null,
    ): void {
        $this->notify($organization, $type, $severity, $title, $message, $acUnit, $device, $deviceEvent, $actionUrl, $occurredAt);
    }
}
