<?php

namespace App\Models;

use App\Enums\EventSource;
use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    use BelongsToOrganization, HasFactory;

    protected $fillable = [
        'organization_id',
        'ac_unit_id',
        'source',
        'action',
        'description',
        'result',
        'user_id',
        'device_id',
        'command_id',
        'device_event_id',
        'occurred_at',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'source' => EventSource::class,
            'occurred_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    public function acUnit(): BelongsTo
    {
        return $this->belongsTo(AcUnit::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    public function command(): BelongsTo
    {
        return $this->belongsTo(AcCommand::class, 'command_id');
    }
}
