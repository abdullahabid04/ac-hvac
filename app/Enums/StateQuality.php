<?php

namespace App\Enums;

enum StateQuality: string
{
    case Reported = 'reported';
    case Inferred = 'inferred';
    case Unknown = 'unknown';
    case Stale = 'stale';

    public function label(): string
    {
        return match ($this) {
            self::Reported => 'Reported by device',
            self::Inferred => 'Inferred from command (not physically verified)',
            self::Unknown => 'Unknown',
            self::Stale => 'Stale',
        };
    }
}
