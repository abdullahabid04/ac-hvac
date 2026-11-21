<?php

namespace App\Services;

use App\Models\Device;
use Illuminate\Support\Str;

class DeviceAuthService
{
    public function issueToken(Device $device): string
    {
        $token = 'acv1_'.Str::random(60);

        $device->update([
            'api_token_hash' => hash('sha256', $token),
            'token_prefix' => substr($token, 0, 12),
        ]);

        return $token;
    }

    public function authenticate(?string $token): ?Device
    {
        if (! $token) {
            return null;
        }

        return Device::query()
            ->where('api_token_hash', hash('sha256', $token))
            ->where('status', 'active')
            ->first();
    }

    /**
     * Authenticate a device by its serial number (temporary/demo only).
     *
     * Serial number is an identifier, NOT a cryptographic secret. This method
     * is provided ONLY as a temporary demonstration mechanism and must NOT be
     * considered production-grade authentication. Software cannot physically
     * block a handheld remote's direct IR beam.
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
