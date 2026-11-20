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

    public function revoke(Device $device): void
    {
        $device->update([
            'api_token_hash' => null,
            'token_prefix' => null,
            'status' => 'revoked',
        ]);
    }
}
