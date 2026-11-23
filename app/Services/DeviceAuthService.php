<?php

namespace App\Services;

use App\Models\Device;

class DeviceAuthService
{
    /**
     * Authenticate a device by its registered serial number.
     *
     * Serial number is an identifier, NOT a cryptographic secret. Software
     * cannot physically block a handheld remote's direct IR beam.
     *
     * @return Device|null Returns the device if found and active, null otherwise.
     */
    public function authenticateBySerialNumber(string $serialNumber): ?Device
    {
        return Device::where('serial_number', $serialNumber)
            ->where('status', 'active')
            ->first();
    }

    public function revoke(Device $device): void
    {
        $device->update([
            'api_token_hash' => null,
            'token_prefix' => null,
            'status' => 'revoked',
        ]);
    }
}
