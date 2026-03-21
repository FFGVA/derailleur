<?php

namespace App\Enums;

enum InvoiceType: string
{
    case Cotisation = 'C';
    case Evenement = 'E';
    case Autre = 'A';

    public function getLabel(): string
    {
        return match ($this) {
            self::Cotisation => 'Cotisation',
            self::Evenement => 'Événement',
            self::Autre => 'Autre',
        };
    }
}
