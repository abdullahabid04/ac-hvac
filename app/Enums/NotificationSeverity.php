<?php

namespace App\Enums;

enum NotificationSeverity: string
{
    case Alert = 'alert';
    case Warning = 'warning';
    case Info = 'info';

    public function label(): string
    {
        return match ($this) {
            self::Alert => 'Alert',
            self::Warning => 'Warning',
            self::Info => 'Information',
        };
    }
}
