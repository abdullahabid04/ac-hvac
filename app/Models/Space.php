<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Space extends Model
{
    use BelongsToOrganization, HasFactory;

    public const TYPES = [
        'room' => 'Room',
        'corridor' => 'Corridor',
        'hall' => 'Hall',
        'gallery' => 'Gallery',
        'section' => 'Section',
        'other' => 'Other area',
    ];

    protected $fillable = [
        'organization_id',
        'floor_id',
        'parent_space_id',
        'name',
        'code',
        'space_type',
        'status',
        'area_sqm',
    ];

    protected function casts(): array
    {
        return ['area_sqm' => 'decimal:2'];
    }

    public function floor(): BelongsTo
    {
        return $this->belongsTo(Floor::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_space_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_space_id');
    }

    public function acUnits(): HasMany
    {
        return $this->hasMany(AcUnit::class);
    }

    public function descendants(): HasMany
    {
        return $this->children()->with('descendants');
    }

    public function childrenRecursive(): HasMany
    {
        return $this->children()->with('childrenRecursive');
    }

    public function typeLabel(): string
    {
        return self::TYPES[$this->space_type] ?? ucfirst($this->space_type);
    }

    public function breadcrumb(): array
    {
        $nodes = [];
        $space = $this;
        while ($space) {
            array_unshift($nodes, ['label' => $space->name, 'type' => 'space', 'id' => $space->id]);
            $space = $space->parent;
        }

        if ($this->floor) {
            array_unshift($nodes, ['label' => $this->floor->name, 'type' => 'floor', 'id' => $this->floor->id]);
            if ($this->floor->building) {
                array_unshift($nodes, ['label' => $this->floor->building->name, 'type' => 'building', 'id' => $this->floor->building->id]);
                if ($this->floor->building->site) {
                    array_unshift($nodes, ['label' => $this->floor->building->site->name, 'type' => 'site', 'id' => $this->floor->building->site->id]);
                }
            }
        }

        return $nodes;
    }

    public function fullPath(): string
    {
        return collect($this->breadcrumb())->pluck('label')->implode(' › ');
    }

    public function allSpaceIds(): array
    {
        $ids = [$this->id];
        foreach ($this->children as $child) {
            $ids = array_merge($ids, $child->allSpaceIds());
        }

        return $ids;
    }
}
