<?php

namespace App\Models;

use App\Enums\CommandStatus;
use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcCommand extends Model
{
    use BelongsToOrganization, HasFactory, HasUuids;

    protected $fillable = [
        'organization_id',
        'ac_unit_id',
        'device_id',
        'requested_by',
        'command_type',
        'desired_state',
        'ir_profile_version_id',
        'status',
        'issued_at',
        'expires_at',
        'published_at',
        'received_at',
        'transmission_at',
        'completed_at',
        'failure_code',
        'failure_message',
        'idempotency_key',
        'correlation_id',
        'client_request_id',
        'device_result',
        'retry_count',
    ];

    protected function casts(): array
    {
        return [
            'desired_state' => 'array',
            'device_result' => 'array',
            'status' => CommandStatus::class,
            'issued_at' => 'datetime',
            'expires_at' => 'datetime',
            'published_at' => 'datetime',
            'received_at' => 'datetime',
            'transmission_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function acUnit(): BelongsTo
    {
        return $this->belongsTo(AcUnit::class);
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function irProfileVersion(): BelongsTo
    {
        return $this->belongsTo(IrProfileVersion::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(DeviceEvent::class, 'command_id');
    }

    public function stateHistory(): HasMany
    {
        return $this->hasMany(AcStateHistory::class, 'command_id');
    }

    public function isExpired(): bool
    {
        return $this->status === CommandStatus::Expired
            || ($this->expires_at && $this->expires_at->isPast() && ! $this->status->isTerminal());
    }
}
