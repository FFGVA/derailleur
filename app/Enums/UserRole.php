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

    public function getColor(): string
    {
        return match ($this) {
            self::Admin => 'danger',
            self::ChefPeloton => 'warning',
        };
    }
}
