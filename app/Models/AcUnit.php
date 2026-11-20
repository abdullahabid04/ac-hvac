<?php

namespace App\Models;

use App\Enums\ConnectivityStatus;
use App\Enums\PowerState;
use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AcUnit extends Model
{
    use BelongsToOrganization, HasFactory;

    protected $fillable = [
        'organization_id',
        'space_id',
        'ac_model_id',
        'ir_profile_version_id',
        'name',
        'asset_code',
        'serial_number',
        'installation_date',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return ['installation_date' => 'date'];
    }

    public function space(): BelongsTo
    {
        return $this->belongsTo(Space::class);
    }

    public function acModel(): BelongsTo
    {
        return $this->belongsTo(AcModel::class);
    }

    public function irProfileVersion(): BelongsTo
    {
        return $this->belongsTo(IrProfileVersion::class);
    }

    public function currentAssignment(): HasOne
    {
        return $this->hasOne(DeviceAssignment::class)->where('is_current', true);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(DeviceAssignment::class);
    }

    public function device(): ?Device
    {
        return $this->currentAssignment?->device;
    }

    public function stateSnapshot(): HasOne
    {
        return $this->hasOne(AcStateSnapshot::class);
    }

    public function stateHistory(): HasMany
    {
        return $this->hasMany(AcStateHistory::class);
    }

    public function commands(): HasMany
    {
        return $this->hasMany(AcCommand::class);
    }

    public function deviceEvents(): HasMany
    {
        return $this->hasMany(DeviceEvent::class);
    }

    public function dailyUsage(): HasMany
    {
        return $this->hasMany(AcDailyUsage::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function powerState(): PowerState
    {
        return $this->stateSnapshot?->power_state ?? PowerState::Unknown;
    }

    public function connectivity(): ConnectivityStatus
    {
        return $this->device()?->connectivity() ?? ConnectivityStatus::Unknown;
    }

    public function locationLabel(): string
    {
        if (! $this->space) {
            return 'Unassigned location';
        }

        $floor = $this->space->floor;
        $building = $floor?->building;
        $site = $building?->site;

        return collect([
            $this->space->name,
            $floor?->name,
            $building?->name,
            $site?->name,
        ])->filter()->implode(', ');
    }

    public function profileSummary(): string
    {
        if (! $this->acModel) {
            return 'Model not recorded';
        }

        $model = $this->acModel->displayName();
        $variant = $this->acModel->remote_variant ? " ({$this->acModel->remote_variant})" : '';

        return $model.$variant;
    }

    public function temperatureMin(): ?int
    {
        return $this->irProfileVersion?->temperature_min_c;
    }

    public function temperatureMax(): ?int
    {
        return $this->irProfileVersion?->temperature_max_c;
    }

    public function temperatureStep(): float
    {
        return (float) ($this->irProfileVersion?->temperature_step_c ?? 1);
    }

    public function supportsProfileControl(): bool
    {
        return $this->irProfileVersion?->isValidated() === true;
    }
}
