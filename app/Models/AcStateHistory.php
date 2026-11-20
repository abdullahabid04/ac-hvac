<?php

namespace App\Models;

use App\Enums\StateQuality;
use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AcStateHistory extends Model
{
    use BelongsToOrganization, HasFactory;

    protected $fillable = [
        'organization_id',
        'ac_unit_id',
        'device_event_id',
        'command_id',
        'source',
        'state_field',
        'old_value',
        'new_value',
        'observed_at',
        'received_at',
        'quality',
    ];

    protected $table = 'ac_state_history';

    protected function casts(): array
    {
        return [
            'old_value' => 'array',
            'new_value' => 'array',
            'quality' => StateQuality::class,
            'observed_at' => 'datetime',
            'received_at' => 'datetime',
        ];
    }

    public function acUnit(): BelongsTo
    {
        return $this->belongsTo(AcUnit::class);
    }

    public function deviceEvent(): BelongsTo
    {
        return $this->belongsTo(DeviceEvent::class);
    }

    public function command(): BelongsTo
    {
        return $this->belongsTo(AcCommand::class);
    }
}
