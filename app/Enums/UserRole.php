<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'A';
    case ChefPeloton = 'C';

    public function getLabel(): string
    {
        return match ($this) {
            self::Admin => 'Administratrice',
            self::ChefPeloton => 'Cheffe de peloton',
        };
    }
}
