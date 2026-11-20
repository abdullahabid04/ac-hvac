<?php

namespace App\Services;

use App\Enums\EventSource;
use App\Enums\NotificationSeverity;
use App\Models\Device;
use Illuminate\Support\Carbon;

class DeviceHealthService
{
    public function __construct(
        private NotificationService $notifications,
        private ActivityService $activity,
    ) {}

    public function recordHeartbeat(Device $device, array $data, ?Carbon $occurredAt = null): void
    {
        $wasOffline = $device->offline_notified_at !== null;

        $device->update([
            'last_seen_at' => $occurredAt ?? now(),
            'offline_notified_at' => null,
            'firmware_version' => $data['firmware_version'] ?? $device->firmware_version,
            'wifi_rssi_dbm' => $data['wifi_rssi_dbmm'] ?? $data['wifi_rssi_dbm'] ?? $device->wifi_rssi_dbm,
        ]);

        if ($wasOffline) {
            $ac = $device->acUnit();
            $this->notifications->notify(
                $device->organization,
                'device_reconnected',
                NotificationSeverity::Info,
                "{$device->name} is back online",
                'The ESP32 controller reported a heartbeat and is connected again. Device connectivity does not confirm AC power state.',
                $ac,
                $device,
                null,
                $ac ? route('acs.show', $ac) : null,
            );

            $this->activity->record(
                $device->organization,
                $ac,
                EventSource::Device,
                'device_reconnected',
                "{$device->name} reconnected and reported a heartbeat.",
                'success',
                device: $device,
            );
        }
    }

    public function detectOfflineDevices(): int
    {
        $threshold = now()->subMinutes((int) config('hvac.device_offline_after_minutes', 5));

        $devices = Device::query()
            ->whereIn('status', ['active'])
            ->whereNotNull('last_seen_at')
            ->where('last_seen_at', '<', $threshold)
            ->whereNull('offline_notified_at')
            ->with('organization', 'currentAssignment.acUnit')
            ->get();

        foreach ($devices as $device) {
            $device->update(['offline_notified_at' => now()]);
            $ac = $device->acUnit();

            $this->notifications->notify(
                $device->organization,
                'device_offline',
                NotificationSeverity::Alert,
                "{$device->name} is offline",
                'No heartbeat has been received within the configured threshold. The AC last-known state is not changed to OFF.',
                $ac,
                $device,
                null,
                $ac ? route('acs.show', $ac) : null,
            );

            $this->activity->record(
                $device->organization,
                $ac,
                EventSource::System,
                'device_offline',
                "{$device->name} stopped reporting heartbeats. AC state is not assumed OFF.",
                'warning',
                device: $device,
            );
        }

        return $devices->count();
    }
}
