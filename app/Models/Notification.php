<?php

namespace App\Models;

use App\Enums\NotificationSeverity;
use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use BelongsToOrganization, HasFactory;

    protected $fillable = [
        'organization_id',
        'user_id',
        'type',
        'severity',
        'title',
        'message',
        'ac_unit_id',
        'device_id',
        'device_event_id',
        'read_at',
        'action_url',
        'occurred_at',
    ];

    protected function casts(): array
    {
        return [
            'severity' => NotificationSeverity::class,
            'read_at' => 'datetime',
            'occurred_at' => 'datetime',
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

    public function isRead(): bool
    {
        return $this->read_at !== null;
    }
}
