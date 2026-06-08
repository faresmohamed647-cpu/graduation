<?php

namespace App\Enums;

enum ApplicationRole: string
{
    case Parent = 'parent';
    case Driver = 'driver';
    case Admin = 'admin';
    case School = 'school';

    public static function values(): array
    {
        return array_map(static fn (self $role) => $role->value, self::cases());
    }
}
