<?php

namespace App\Enums;

enum PowerState: string
{
    case KnownOn = 'known_on';
    case KnownOff = 'known_off';
    case Unknown = 'unknown';
    case Stale = 'stale';

    public function label(): string
    {
        return match ($this) {
            self::KnownOn => 'Known ON',
            self::KnownOff => 'Known OFF',
            self::Unknown => 'Unknown',
            self::Stale => 'Stale',
        };
    }

    public function shortLabel(): string
    {
        return match ($this) {
            self::KnownOn => 'ON',
            self::KnownOff => 'OFF',
            self::Unknown => 'Unknown',
            self::Stale => 'Stale',
        };
    }

    public function isOn(): bool
    {
        return $this === self::KnownOn;
    }
}
