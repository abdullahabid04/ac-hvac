<?php

namespace App\Services;

use App\Enums\PowerState;
use App\Models\AcUnit;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;

class OnDurationCalculator
{
    /**
     * Calculate known ON time and unknown time for one AC in a UTC range.
     *
     * ON duration is an operating-time estimate. It is never electricity
     * consumption. Unknown/stale gaps are tracked separately and never
     * counted as confirmed OFF time.
     *
     * @return array{on_seconds:int, unknown_seconds:int, incomplete:bool, final_state:string, event_count:int, intervals:array}
     */
    public function calculate(AcUnit $ac, CarbonInterface $from, CarbonInterface $to): array
    {
        $from = Carbon::instance($from)->utc();
        $to = Carbon::instance($to)->utc();
        $effectiveTo = $to->isFuture() ? now()->utc() : $to;

        if ($effectiveTo->lessThanOrEqualTo($from)) {
            return [
                'on_seconds' => 0,
                'unknown_seconds' => 0,
                'incomplete' => false,
                'final_state' => PowerState::Unknown->value,
                'event_count' => 0,
                'intervals' => [],
            ];
        }

        $history = $ac->stateHistory()
            ->where('state_field', 'power_state')
            ->where('observed_at', '<=', $effectiveTo)
            ->orderBy('observed_at')
            ->orderBy('id')
            ->get();

        $before = $history->filter(fn ($event) => $event->observed_at->lt($from));
        $during = $history->filter(fn ($event) => $event->observed_at->gte($from));

        $state = PowerState::Unknown;
        $lastKnownAt = null;

        if ($before->isNotEmpty()) {
            $last = $before->last();
            $state = $this->extractPower($last->new_value) ?? PowerState::Unknown;
            $lastKnownAt = $last->observed_at;
        } elseif ($ac->stateSnapshot?->observed_at && $ac->stateSnapshot->observed_at->lt($from)) {
            $state = $ac->stateSnapshot->power_state ?? PowerState::Unknown;
            $lastKnownAt = $ac->stateSnapshot->observed_at;
        }

        $onSeconds = 0;
        $unknownSeconds = 0;
        $incomplete = false;
        $intervals = [];

        $cursor = $from->copy();
        $stateAtStart = $state;

        if ($stateAtStart === PowerState::Unknown || $stateAtStart === PowerState::Stale) {
            // The state before the window is unknown: flag the affected part.
            $incomplete = true;
        }

        foreach ($during as $event) {
            $eventAt = $event->observed_at->copy();

            if ($eventAt->greaterThan($effectiveTo)) {
                break;
            }

            if ($eventAt->greaterThan($cursor)) {
                [$seconds, $unknown] = $this->accumulate($state, $cursor, $eventAt);
                $onSeconds += $seconds;
                $unknownSeconds += $unknown;

                if ($seconds > 0 || $unknown > 0) {
                    $intervals[] = [
                        'from' => $cursor->toIso8601String(),
                        'to' => $eventAt->toIso8601String(),
                        'state' => $state->value,
                        'seconds' => $seconds,
                    ];
                }

                if ($lastKnownAt && $cursor->diffInMinutes($eventAt) > 360 && $state === PowerState::KnownOn) {
                    $incomplete = true;
                }
            }

            $nextState = $this->extractPower($event->new_value) ?? PowerState::Unknown;

            if ($nextState === PowerState::Unknown || $nextState === PowerState::Stale) {
                $incomplete = true;
            }

            $state = $nextState;
            $lastKnownAt = $eventAt;
            $cursor = $eventAt;
        }

        if ($effectiveTo->greaterThan($cursor)) {
            [$seconds, $unknown] = $this->accumulate($state, $cursor, $effectiveTo);
            $onSeconds += $seconds;
            $unknownSeconds += $unknown;

            if ($seconds > 0 || $unknown > 0) {
                $intervals[] = [
                    'from' => $cursor->toIso8601String(),
                    'to' => $effectiveTo->toIso8601String(),
                    'state' => $state->value,
                    'seconds' => $seconds,
                ];
            }

            if ($state === PowerState::KnownOn) {
                $incomplete = true; // provisional interval to reporting time
            }
        }

        return [
            'on_seconds' => max(0, $onSeconds),
            'unknown_seconds' => max(0, $unknownSeconds),
            'incomplete' => $incomplete,
            'final_state' => $state->value,
            'event_count' => $during->count(),
            'intervals' => $intervals,
        ];
    }

    /**
     * @return array{0:int,1:int} [on_seconds, unknown_seconds]
     */
    private function accumulate(PowerState $state, Carbon $from, Carbon $to): array
    {
        $seconds = max(0, $to->getTimestamp() - $from->getTimestamp());

        return match ($state) {
            PowerState::KnownOn => [$seconds, 0],
            PowerState::Unknown, PowerState::Stale => [0, $seconds],
            PowerState::KnownOff => [0, 0],
        };
    }

    private function extractPower(mixed $value): ?PowerState
    {
        if ($value instanceof PowerState) {
            return $value;
        }

        if (is_array($value)) {
            $value = $value['power'] ?? $value['value'] ?? null;
        }

        if (is_string($value)) {
            return PowerState::tryFrom($value);
        }

        return null;
    }
}
