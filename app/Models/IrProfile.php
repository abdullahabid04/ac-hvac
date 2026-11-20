<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IrProfile extends Model
{
    use HasFactory;

    protected $fillable = ['ac_model_id', 'name', 'status'];

    public function acModel(): BelongsTo
    {
        return $this->belongsTo(AcModel::class);
    }

    public function versions(): HasMany
    {
        return $this->hasMany(IrProfileVersion::class)->orderByDesc('version_number');
    }

    public function validatedVersions(): HasMany
    {
        return $this->versions()->where('validation_status', 'validated');
    }
}
