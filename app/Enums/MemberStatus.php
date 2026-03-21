<?php

namespace App\Enums;

enum MemberStatus: string
{
    case Brouillon = 'D';
    case Actif = 'A';
    case Inactif = 'I';
    case EnAttente = 'P';

    public function getLabel(): string
    {
        return match ($this) {
            self::Brouillon => 'Brouillon',
            self::Actif => 'Active',
            self::Inactif => 'Inactive',
            self::EnAttente => 'En attente',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Brouillon => 'gray',
            self::Actif => 'success',
            self::Inactif => 'danger',
            self::EnAttente => 'warning',
        };
    }
}
