<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcModel extends Model
{
    use HasFactory;

    protected $fillable = [
        'manufacturer',
        'model_name',
        'remote_variant',
        'protocol_name',
        'notes',
    ];

    public function profiles(): HasMany
    {
        return $this->hasMany(IrProfile::class);
    }

    public function acUnits(): HasMany
    {
        return $this->hasMany(AcUnit::class);
    }

    public function displayName(): string
    {
        return "{$this->manufacturer} {$this->model_name}";
    }
}
