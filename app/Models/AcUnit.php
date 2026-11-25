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
        'desired_power_state',
        'desired_temperature_setpoint_c',
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

    /**
     * Get the desired power state set by the dashboard.
     * Returns 'on', 'off', or null if not set.
     */
    public function desiredPowerState(): ?string
    {
        return $this->desired_power_state;
    }

    /**
     * Get the desired temperature setpoint set by the dashboard.
     */
    public function desiredTemperatureSetpointC(): ?int
    {
        return $this->desired_temperature_setpoint_c;
    }

    /**
     * Get the latest desired power state as a boolean, or null.
     */
    public function desiredPowerStateBool(): ?bool
    {
        if ($this->desired_power_state === null) {
            return null;
        }
        return strtolower((string) $this->desired_power_state) === 'on';
    }

    /**
     * Check if the AC has a pending desired state change that differs from current.
     */
    public function hasPendingDesiredStateChange(): bool
    {
        if ($this->desired_power_state === null && $this->desired_temperature_setpoint_c === null) {
            return false;
        }

        $snapshot = $this->stateSnapshot;
        $currentPower = $snapshot?->power_state?->value;
        $currentSetpoint = $snapshot?->temperature_setpoint_c;

        if ($this->desired_power_state !== null && strtolower((string) $this->desired_power_state) !== $currentPower) {
            return true;
        }

        if ($this->desired_temperature_setpoint_c !== null && $this->desired_temperature_setpoint_c !== $currentSetpoint) {
            return true;
        }

        return false;
    }
}
