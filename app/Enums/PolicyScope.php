<?php

namespace App\Enums;

enum PolicyScope: string
{
    case Organization = 'organization';
    case Site = 'site';
    case Building = 'building';
    case Floor = 'floor';
    case Space = 'space';
    case Ac = 'ac';

    public function label(): string
    {
        return match ($this) {
            self::Organization => 'Organization',
            self::Site => 'Site',
            self::Building => 'Building',
            self::Floor => 'Floor',
            self::Space => 'Space',
            self::Ac => 'Air conditioner',
        };
    }
}
