<?php

namespace App\Support;

use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;

class Format
{
    public static function duration(int|float|null $seconds, bool $compact = false): string
    {
        $seconds = (int) round($seconds ?? 0);

        if ($seconds <= 0) {
            return $compact ? '0h' : '0h 0m';
        }

        $hours = intdiv($seconds, 3600);
        $minutes = intdiv($seconds % 3600, 60);

        if ($compact) {
            return $hours > 0 ? "{$hours}h" : "{$minutes}m";
        }

        if ($hours > 0) {
            return "{$hours}h {$minutes}m";
        }

        return "{$minutes}m";
    }

    public static function datetime(?CarbonInterface $value, string $timezone = 'UTC', string $format = 'd M Y, H:i'): string
    {
        if (! $value) {
            return '—';
        }

        return Carbon::instance($value)->setTimezone($timezone)->format($format);
    }

    public static function time(?CarbonInterface $value, string $timezone = 'UTC'): string
    {
        if (! $value) {
            return '—';
        }

        return Carbon::instance($value)->setTimezone($timezone)->format('H:i');
    }

    public static function relative(?CarbonInterface $value): string
    {
        if (! $value) {
            return 'Never';
        }

        return Carbon::instance($value)->diffForHumans(short: true);
    }

    public static function percent(int|float $part, int|float $total, int $precision = 0): string
    {
        if ($total <= 0) {
            return '0%';
        }

        return number_format(($part / $total) * 100, $precision).'%';
    }

    public static function temperature(?int $value): string
    {
        return $value === null ? '—' : $value.'°C';
    }
}
