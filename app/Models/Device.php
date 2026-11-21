<?php

namespace App\Models;

use App\Enums\ConnectivityStatus;
use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Device extends Model
{
    use BelongsToOrganization, HasFactory;

    protected $fillable = [
        'organization_id',
        'device_uid',
        'serial_number',
        'command_representation',
        'name',
        'hardware_model',
        'firmware_version',
        'status',
        'api_token_hash',
        'token_prefix',
        'last_seen_at',
        'offline_notified_at',
        'last_ip',
        'wifi_rssi_dbm',
        'effective_remote_policy',
        'policy_version',
        'policy_synced_at',
        'local_config',
    ];

    protected $hidden = ['api_token_hash'];

    protected function casts(): array
    {
        return [
            'last_seen_at' => 'datetime',
            'offline_notified_at' => 'datetime',
            'policy_synced_at' => 'datetime',
            'local_config' => 'array',
        ];
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(DeviceAssignment::class);
    }

    public function currentAssignment(): HasOne
    {
        return $this->hasOne(DeviceAssignment::class)->where('is_current', true);
    }

    public function acUnit(): ?AcUnit
    {
        return $this->currentAssignment?->acUnit;
    }

    public function commands(): HasMany
    {
        return $this->hasMany(AcCommand::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(DeviceEvent::class);
    }

    public function connectivity(): ConnectivityStatus
    {
        if (in_array($this->status, ['revoked', 'suspended', 'unprovisioned'], true)) {
            return ConnectivityStatus::Offline;
        }

        if (! $this->last_seen_at) {
            return ConnectivityStatus::Unknown;
        }

        $threshold = (int) config('hvac.device_offline_after_minutes', 5);

        return $this->last_seen_at->greaterThanOrEqualTo(now()->subMinutes($threshold))
            ? ConnectivityStatus::Online
            : ConnectivityStatus::Offline;
    }
}
