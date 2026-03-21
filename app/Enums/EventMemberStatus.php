<?php

namespace App\Enums;

enum EventMemberStatus: string
{
    case Inscrit = 'N';
    case Confirme = 'C';
    case Annule = 'X';

    public function getLabel(): string
    {
        return match ($this) {
            self::Inscrit => 'Inscrite',
            self::Confirme => 'Confirmée',
            self::Annule => 'Annulée',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Inscrit => 'warning',
            self::Confirme => 'success',
            self::Annule => 'danger',
        };
    }
}
