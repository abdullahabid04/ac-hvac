<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Organization extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'timezone',
        'status',
        'contact_email',
        'contact_phone',
        'address',
        'settings',
    ];

    protected function casts(): array
    {
        return [
            'settings' => 'array',
        ];
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot(['role', 'is_active'])
            ->withTimestamps();
    }

    public function sites(): HasMany
    {
        return $this->hasMany(Site::class);
    }

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

    public function acUnits(): HasMany
    {
        return $this->hasMany(AcUnit::class);
    }

    public function devices(): HasMany
    {
        return $this->hasMany(Device::class);
    }

    public function commands(): HasMany
    {
        return $this->hasMany(AcCommand::class);
    }

    public function deviceEvents(): HasMany
    {
        return $this->hasMany(DeviceEvent::class);
    }

    public function remotePolicies(): HasMany
    {
        return $this->hasMany(RemotePolicy::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(GeneratedReport::class);
    }

    public function dailyUsage(): HasMany
    {
        return $this->hasMany(AcDailyUsage::class);
    }

    public function setting(string $key, mixed $default = null): mixed
    {
        return data_get($this->settings ?? [], $key, $default);
    }
}
