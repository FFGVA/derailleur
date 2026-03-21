<?php

namespace App\Enums;

enum PhoneLabel: string
{
    case MobilePrincipal = 'Mobile principal';
    case MobileSecondaire = 'Mobile secondaire';
    case Maison = 'Maison';
    case Travail = 'Travail';
    case Autre = 'Autre';

    public function priority(): int
    {
        return match ($this) {
            self::MobilePrincipal => 1,
            self::MobileSecondaire => 2,
            self::Maison => 3,
            self::Travail => 4,
            self::Autre => 5,
        };
    }

    public static function sortPhones(\Illuminate\Support\Collection $phones): \Illuminate\Support\Collection
    {
        return $phones->sortBy(function ($phone) {
            $label = self::tryFrom($phone->label ?? '');
            return $label ? $label->priority() : 99;
        })->values();
    }
}
