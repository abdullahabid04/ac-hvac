<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeviceEvent extends Model
{
    use BelongsToOrganization, HasFactory, HasUuids;

    protected $fillable = [
        'event_uid',
        'organization_id',
        'device_id',
        'ac_unit_id',
        'command_id',
        'event_type',
        'source',
        'classification',
        'device_occurred_at',
        'server_received_at',
        'schema_version',
        'payload',
        'processing_status',
    ];

    protected function casts(): array
    {
        return [
            'device_occurred_at' => 'datetime',
            'server_received_at' => 'datetime',
            'payload' => 'array',
        ];
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    public function acUnit(): BelongsTo
    {
        return $this->belongsTo(AcUnit::class);
    }

    public function command(): BelongsTo
    {
        return $this->belongsTo(AcCommand::class, 'command_id');
    }

    public function isSimulated(): bool
    {
        return (bool) data_get($this->payload, 'simulated', false);
    }
}
