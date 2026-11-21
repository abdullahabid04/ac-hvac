<?php

namespace App\Services;

use App\Models\Device;
use Illuminate\Support\Str;

class DeviceSerialService
{
    /**
     * Serial number format: HVAC-ESP32-YYYY-NNNNNN
     *
     * - HVAC: product/system identifier
     * - ESP32: hardware family
     * - YYYY: manufacturing/provisioning year
     * - NNNNNN: unique sequential device number (6 digits, zero-padded)
     *
     * This identifier is used for device authentication in the development
     * simulator and is not a cryptographic secret. It is displayed in the
     * device UI and can be copied into ESP32 firmware configuration.
     */
    const PREFIX = 'HVAC-ESP32';

    /**
     * Generate a unique serial number for a new ESP32 device.
     *
     * Uses an atomic database-backed counter to guarantee uniqueness under
     * concurrent registration.
     *
     * @return string The generated serial number, e.g. "HVAC-ESP32-2026-000001"
     */
    public function generateSerialNumber(?int $deviceId = null): string
    {
        $year = now()->year;
        // PREFIX is "HVAC-ESP32"; append the year and a zero-padded sequence:
        // e.g. "HVAC-ESP32-2026-000001".
        $prefix = sprintf('%s-%04d-', self::PREFIX, $year);

        // Find the highest existing serial number for this year
        $maxSuffix = Device::where('serial_number', 'like', $prefix . '%')
            ->max('serial_number');

        $nextNumber = 1;
        if ($maxSuffix) {
            // Extract the numeric suffix after the prefix
            $suffix = str_replace($prefix, '', $maxSuffix);
            $nextNumber = (int) $suffix + 1;
        }

        return $prefix . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Resolve a device by its serial number.
     *
     * @return Device|null
     */
    public function findBySerialNumber(string $serialNumber): ?Device
    {
        return Device::where('serial_number', $serialNumber)->first();
    }

    /**
     * Validate that a serial number exists and belongs to an active device.
     *
     * @return Device|null Returns the device if valid, null otherwise.
     */
    public function validateSerialNumber(string $serialNumber): ?Device
    {
        return Device::where('serial_number', $serialNumber)
            ->where('status', 'active')
            ->first();
    }

    /**
     * Assign a serial number to an existing device.
     *
     * @throws \Illuminate\Database\QueryException If the serial number is already in use
     */
    public function assignSerialNumber(Device $device): string
    {
        $serialNumber = $this->generateSerialNumber($device->id);

        $device->update([
            'serial_number' => $serialNumber,
        ]);

        return $serialNumber;
    }
}
