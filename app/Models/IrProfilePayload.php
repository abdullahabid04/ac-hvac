<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IrProfilePayload extends Model
{
    use HasFactory;

    protected $fillable = [
        'ir_profile_version_id',
        'action_key',
        'representation_type',
        'payload',
        'carrier_frequency_hz',
        'repeat_metadata',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'repeat_metadata' => 'array',
        ];
    }

    public function version(): BelongsTo
    {
        return $this->belongsTo(IrProfileVersion::class, 'ir_profile_version_id');
    }
}
