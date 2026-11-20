<?php

namespace App\Models;

use App\Enums\PowerState;
use App\Enums\StateQuality;
use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AcStateSnapshot extends Model
{
    use BelongsToOrganization, HasFactory;

    protected $fillable = [
        'organization_id',
        'ac_unit_id',
        'power_state',
        'temperature_setpoint_c',
        'operating_mode',
        'fan_setting',
        'swing_setting',
        'source',
        'observed_at',
        'received_at',
        'quality',
        'ir_profile_version_id',
    ];

    protected function casts(): array
    {
        return [
            'power_state' => PowerState::class,
            'quality' => StateQuality::class,
            'observed_at' => 'datetime',
            'received_at' => 'datetime',
        ];
    }

    public function acUnit(): BelongsTo
    {
        return $this->belongsTo(AcUnit::class);
    }

    public function isStale(): bool
    {
        if (! $this->observed_at) {
            return true;
        }

        $minutes = (int) config('hvac.state_stale_after_minutes', 120);

        return $this->observed_at->lt(now()->subMinutes($minutes));
    }
}
