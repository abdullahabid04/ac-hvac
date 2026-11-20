<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'job_title',
        'phone',
        'notification_preferences',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'notification_preferences' => 'array',
            'last_login_at' => 'datetime',
        ];
    }

    public function organizations(): BelongsToMany
    {
        return $this->belongsToMany(Organization::class)
            ->withPivot(['role', 'is_active'])
            ->withTimestamps();
    }

    public function currentOrganization(): ?Organization
    {
        if ($this->relationLoaded('organizations')) {
            return $this->organizations->first(fn (Organization $organization) => (bool) $organization->pivot->is_active);
        }

        return $this->organizations()
            ->wherePivot('is_active', true)
            ->first();
    }

    public function belongsToOrganization(Organization|int $organization): bool
    {
        $organizationId = $organization instanceof Organization ? $organization->id : $organization;

        return $this->organizations()
            ->wherePivot('is_active', true)
            ->whereKey($organizationId)
            ->exists();
    }

    public function commands(): HasMany
    {
        return $this->hasMany(AcCommand::class, 'requested_by');
    }

    public function notificationPreferences(): HasMany
    {
        return $this->hasMany(NotificationPreference::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }
}
