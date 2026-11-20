<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Building extends Model
{
    use BelongsToOrganization, HasFactory;

    protected $fillable = ['organization_id', 'site_id', 'name', 'code', 'status'];

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function floors(): HasMany
    {
        return $this->hasMany(Floor::class);
    }

    public function spaces(): HasMany
    {
        return $this->hasMany(Space::class);
    }
}
