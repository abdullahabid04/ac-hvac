<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Site extends Model
{
    use BelongsToOrganization, HasFactory;

    protected $fillable = ['organization_id', 'name', 'code', 'address', 'timezone', 'status'];

    public function buildings(): HasMany
    {
        return $this->hasMany(Building::class);
    }

    public function floors(): HasMany
    {
        return $this->hasMany(Floor::class);
    }

    public function spaces(): HasMany
    {
        return $this->hasMany(Space::class);
    }

    public function acUnits()
    {
        return AcUnit::query()
            ->where('organization_id', $this->organization_id)
            ->whereHas('space.floor.building', fn ($query) => $query->where('site_id', $this->id));
    }

    public function floorIds(): array
    {
        return $this->floors()->pluck('id')->all();
    }

    public function spaceIds(): array
    {
        return $this->spaces()->pluck('id')->all();
    }
}
