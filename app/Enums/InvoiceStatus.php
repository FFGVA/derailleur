<?php

namespace App\Enums;

enum InvoiceStatus: string
{
    case New = 'N';
    case Sent = 'E';
    case Paid = 'P';
    case Cancelled = 'X';

    public function getLabel(): string
    {
        return match ($this) {
            self::New => 'Nouvelle',
            self::Sent => 'Envoyée',
            self::Paid => 'Payée',
            self::Cancelled => 'Annulée',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::New => 'warning',
            self::Sent => 'info',
            self::Paid => 'success',
            self::Cancelled => 'danger',
        };
    }
}
