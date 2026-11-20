<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AcDailyUsage extends Model
{
    use BelongsToOrganization, HasFactory;

    protected $table = 'ac_daily_usage';

    protected $fillable = [
        'organization_id',
        'ac_unit_id',
        'usage_date',
        'on_seconds',
        'unknown_seconds',
        'incomplete',
        'calculated_at',
    ];

    protected function casts(): array
    {
        return [
            'usage_date' => 'date',
            'incomplete' => 'boolean',
            'calculated_at' => 'datetime',
        ];
    }

    public function acUnit(): BelongsTo
    {
        return $this->belongsTo(AcUnit::class);
    }
}
