<?php

namespace App\Enums;

enum CommandStatus: string
{
    case Created = 'created';
    case Published = 'published';
    case Received = 'received';
    case TransmissionAttempted = 'transmission_attempted';
    case Completed = 'completed';
    case Failed = 'failed';
    case Expired = 'expired';

    public function label(): string
    {
        return match ($this) {
            self::Created => 'Created',
            self::Published => 'Queued for device',
            self::Received => 'Received by device',
            self::TransmissionAttempted => 'IR transmission attempted',
            self::Completed => 'Device completed IR transmission',
            self::Failed => 'Failed',
            self::Expired => 'Expired',
        };
    }

    public function isTerminal(): bool
    {
        return in_array($this, [self::Completed, self::Failed, self::Expired], true);
    }

    public function isPending(): bool
    {
        return in_array($this, [self::Created, self::Published, self::Received, self::TransmissionAttempted], true);
    }
}
