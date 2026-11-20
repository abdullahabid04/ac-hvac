<?php

namespace App\Services;

use App\Models\AcUnit;
use Illuminate\Validation\ValidationException;

class AcCapabilityValidator
{
    /**
     * Validate a requested desired state against the AC's active IR profile.
     *
     * @return array<string, mixed>
     */
    public function validateDesiredState(AcUnit $ac, array $desiredState): array
    {
        $ac->loadMissing(['irProfileVersion', 'acModel', 'currentAssignment.device', 'stateSnapshot']);

        if ($ac->status !== 'active') {
            throw ValidationException::withMessages(['ac' => 'This air conditioner is not active.']);
        }

        if (! $ac->currentAssignment?->device) {
            throw ValidationException::withMessages(['device' => 'No device is assigned to this air conditioner.']);
        }

        $profile = $ac->irProfileVersion;

        if (! $profile) {
            throw ValidationException::withMessages(['profile' => 'No IR profile is assigned to this air conditioner.']);
        }

        if (! $profile->isValidated()) {
            throw ValidationException::withMessages(['profile' => 'The assigned IR profile is not validated for production commands.']);
        }

        $allowedKeys = ['power', 'temperature_setpoint_c', 'mode', 'fan', 'swing'];
        $unknown = array_diff(array_keys($desiredState), $allowedKeys);

        if ($unknown !== []) {
            throw ValidationException::withMessages(['desired_state' => 'Unsupported state fields: '.implode(', ', $unknown)]);
        }

        $normalized = [];

        if (array_key_exists('power', $desiredState)) {
            $power = strtolower((string) $desiredState['power']);
            if (! in_array($power, ['on', 'off'], true)) {
                throw ValidationException::withMessages(['power' => 'Power must be either "on" or "off".']);
            }
            $normalized['power'] = $power;
        }

        if (array_key_exists('temperature_setpoint_c', $desiredState)) {
            $temperature = $desiredState['temperature_setpoint_c'];

            if (! is_numeric($temperature)) {
                throw ValidationException::withMessages(['temperature_setpoint_c' => 'Temperature setpoint must be numeric.']);
            }

            $temperature = (int) $temperature;

            if ($temperature < $profile->temperature_min_c || $temperature > $profile->temperature_max_c) {
                throw ValidationException::withMessages([
                    'temperature_setpoint_c' => "The setpoint must be between {$profile->temperature_min_c}°C and {$profile->temperature_max_c}°C for this profile.",
                ]);
            }

            if ($profile->temperature_step_c > 0 && fmod($temperature, (float) $profile->temperature_step_c) !== 0.0) {
                throw ValidationException::withMessages([
                    'temperature_setpoint_c' => "The setpoint must use {$profile->temperature_step_c}°C steps for this profile.",
                ]);
            }

            $normalized['temperature_setpoint_c'] = $temperature;
        }

        foreach (['mode' => 'operating_modes', 'fan' => 'fan_speeds', 'swing' => 'swing'] as $field => $key) {
            if (! array_key_exists($field, $desiredState)) {
                continue;
            }

            $supported = data_get($profile->capabilities, $key, []);

            if (! is_array($supported) || $supported === []) {
                throw ValidationException::withMessages([$field => "This profile does not support {$field} control."]);
            }

            $value = (string) $desiredState[$field];

            if (! in_array($value, $supported, true)) {
                throw ValidationException::withMessages([$field => "Unsupported {$field} value for this profile."]);
            }

            $normalized[$field] = $value;
        }

        if ($normalized === []) {
            throw ValidationException::withMessages(['desired_state' => 'At least one supported state value is required.']);
        }

        return $normalized;
    }

    public function powerStateLabel(array $desiredState): string
    {
        return match ($desiredState['power'] ?? null) {
            'on' => 'ON',
            'off' => 'OFF',
            default => 'state update',
        };
    }
}
