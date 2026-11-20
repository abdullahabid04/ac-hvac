<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Floor extends Model
{
    use BelongsToOrganization, HasFactory;

    protected $fillable = ['organization_id', 'building_id', 'name', 'floor_number', 'status'];

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    public function spaces(): HasMany
    {
        return $this->hasMany(Space::class);
    }

    public function topLevelSpaces(): HasMany
    {
        return $this->spaces()->whereNull('parent_space_id');
    }
}
