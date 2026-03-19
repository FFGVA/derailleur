<?php

namespace App\Enums;

enum EventStatus: string
{
    case Nouveau = 'N';
    case Publie = 'P';
    case Annule = 'X';
    case Termine = 'T';

    public function getLabel(): string
    {
        return match ($this) {
            self::Nouveau => 'Nouveau',
            self::Publie => 'Publié',
            self::Annule => 'Annulé',
            self::Termine => 'Terminé',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Nouveau => 'info',
            self::Publie => 'success',
            self::Annule => 'danger',
            self::Termine => 'gray',
        };
    }
}
