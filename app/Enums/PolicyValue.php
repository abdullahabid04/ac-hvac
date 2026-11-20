<?php

namespace App\Enums;

enum PolicyValue: string
{
    case Inherit = 'inherit';
    case Enabled = 'enabled';
    case Disabled = 'disabled';

    public function label(): string
    {
        return ucfirst($this->value);
    }
}
