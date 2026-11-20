<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IrProfileVersion extends Model
{
    use HasFactory;

    protected $fillable = [
        'ir_profile_id',
        'version_number',
        'protocol_metadata',
        'capabilities',
        'temperature_min_c',
        'temperature_max_c',
        'temperature_step_c',
        'validation_status',
        'validation_notes',
        'validated_at',
    ];

    protected function casts(): array
    {
        return [
            'protocol_metadata' => 'array',
            'capabilities' => 'array',
            'temperature_step_c' => 'decimal:1',
            'validated_at' => 'datetime',
        ];
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(IrProfile::class, 'ir_profile_id');
    }

    public function payloads(): HasMany
    {
        return $this->hasMany(IrProfilePayload::class);
    }

    public function isValidated(): bool
    {
        return $this->validation_status === 'validated';
    }

    public function supports(string $capability): bool
    {
        return (bool) data_get($this->capabilities, $capability, false);
    }

    public function supportsTemperature(int $temperature): bool
    {
        return $temperature >= $this->temperature_min_c && $temperature <= $this->temperature_max_c;
    }
}
