<?php

namespace App\Services;

use App\Enums\EventSource;
use App\Models\AcUnit;
use App\Models\Device;
use App\Models\DeviceAssignment;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DeviceAssignmentService
{
    public function __construct(
        private ActivityService $activity,
        private AuditService $audit,
    ) {}

    public function assign(Organization $organization, AcUnit $ac, ?Device $device, User $user): void
    {
        if ($ac->organization_id !== $organization->id) {
            abort(404);
        }

        DB::transaction(function () use ($organization, $ac, $device, $user) {
            $current = DeviceAssignment::where('ac_unit_id', $ac->id)
                ->where('is_current', true)
                ->first();

            if ($current && $current->device_id === $device?->id) {
                return;
            }

            if ($device) {
                if ($device->organization_id !== $organization->id) {
                    throw ValidationException::withMessages(['device_id' => 'The selected device does not belong to this organization.']);
                }

                $otherAssignment = DeviceAssignment::where('device_id', $device->id)
                    ->where('is_current', true)
                    ->where('ac_unit_id', '!=', $ac->id)
                    ->first();

                if ($otherAssignment) {
                    throw ValidationException::withMessages([
                        'device_id' => 'This device is already assigned to another air conditioner.',
                    ]);
                }
            }

            if ($current) {
                $current->update([
                    'is_current' => false,
                    'unassigned_at' => now(),
                ]);
            }

            if ($device) {
                DeviceAssignment::create([
                    'organization_id' => $organization->id,
                    'device_id' => $device->id,
                    'ac_unit_id' => $ac->id,
                    'assigned_at' => now(),
                    'is_current' => true,
                    'assigned_by' => $user->id,
                ]);

                $this->audit->record(
                    $organization,
                    $user,
                    'device.assigned',
                    'ac_unit',
                    $ac->id,
                    ['device_id' => $current?->device_id],
                    ['device_id' => $device->id],
                    request(),
                );
            }

            $this->activity->record(
                $organization,
                $ac,
                EventSource::Portal,
                $device ? 'device_assigned' : 'device_unassigned',
                $device
                    ? "{$device->name} assigned to {$ac->asset_code}."
                    : "Device unassigned from {$ac->asset_code}.",
                'success',
                $user,
                $device,
            );
        });
    }
}
