<?php

namespace App\Enums;

enum ConnectivityStatus: string
{
    case Online = 'online';
    case Offline = 'offline';
    case Unknown = 'unknown';

    public function label(): string
    {
        return ucfirst($this->value);
    }
}
