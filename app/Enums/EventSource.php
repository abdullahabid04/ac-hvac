<?php

namespace App\Enums;

enum EventSource: string
{
    case Portal = 'portal';
    case PhysicalRemote = 'physical_remote';
    case Device = 'device';
    case System = 'system';

    public function label(): string
    {
        return match ($this) {
            self::Portal => 'Web Portal',
            self::PhysicalRemote => 'Physical Remote',
            self::Device => 'ESP32 / Device',
            self::System => 'Backend / System',
        };
    }
}
