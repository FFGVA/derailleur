<?php

namespace App\Enums;

enum MemberStatus: string
{
    case Brouillon = 'D';
    case Actif = 'A';
    case Inactif = 'I';
    case EnAttente = 'P';
    case NonMembre = 'N';
    case Enfant = 'E';

    public function getLabel(): string
    {
        return match ($this) {
            self::Brouillon => 'Brouillon',
            self::Actif => 'Active',
            self::Inactif => 'Inactive',
            self::EnAttente => 'En attente',
            self::NonMembre => 'Non-membre',
            self::Enfant => 'Active (mineure)',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Brouillon => 'gray',
            self::Actif => 'success',
            self::Inactif => 'danger',
            self::EnAttente => 'warning',
            self::NonMembre => 'info',
            self::Enfant => 'success',
        };
    }
}
