<?php

namespace App\Enums;

enum EventType: string
{
    case WeekendRide = 'W';
    case AfterWork = 'A';
    case RookieRide = 'R';
    case Entrainement = 'E';
    case ProjetSpecial = 'P';
    case Atelier = 'T';
    case SkillsClinic = 'S';
    case Reunion = 'U';
    case Course = 'C';

    public function getLabel(): string
    {
        return match ($this) {
            self::WeekendRide => 'Weekend Ride',
            self::AfterWork => 'After work',
            self::RookieRide => 'Rookie ride',
            self::Entrainement => 'Entraînement',
            self::ProjetSpecial => 'Projet spécial',
            self::Atelier => 'Atelier',
            self::SkillsClinic => 'Skills clinic',
            self::Reunion => 'Réunion',
            self::Course => 'Course',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::WeekendRide => '#2d6a4f',
            self::AfterWork => '#e76f51',
            self::RookieRide => '#80081C',
            self::Entrainement => '#52b788',
            self::ProjetSpecial => '#7b2d8e',
            self::Atelier => '#4cc9f0',
            self::SkillsClinic => '#3a56d4',
            self::Reunion => '#d62828',
            self::Course => '#d4a843',
        };
    }
}
