<?php

namespace Tests\Unit\Enums;

use App\Enums\EventType;
use PHPUnit\Framework\TestCase;

class EventTypeTest extends TestCase
{
    public function test_all_cases_exist(): void
    {
        $cases = EventType::cases();

        $this->assertCount(9, $cases);
        $this->assertNotNull(EventType::from('W'));
        $this->assertNotNull(EventType::from('A'));
        $this->assertNotNull(EventType::from('R'));
        $this->assertNotNull(EventType::from('E'));
        $this->assertNotNull(EventType::from('P'));
        $this->assertNotNull(EventType::from('T'));
        $this->assertNotNull(EventType::from('S'));
        $this->assertNotNull(EventType::from('U'));
        $this->assertNotNull(EventType::from('C'));
    }

    public function test_case_values(): void
    {
        $this->assertSame('W', EventType::WeekendRide->value);
        $this->assertSame('A', EventType::AfterWork->value);
        $this->assertSame('R', EventType::RookieRide->value);
        $this->assertSame('E', EventType::Entrainement->value);
        $this->assertSame('P', EventType::ProjetSpecial->value);
        $this->assertSame('T', EventType::Atelier->value);
        $this->assertSame('S', EventType::SkillsClinic->value);
        $this->assertSame('U', EventType::Reunion->value);
        $this->assertSame('C', EventType::Course->value);
    }

    public function test_get_label_returns_french_labels(): void
    {
        $this->assertSame('Weekend Ride', EventType::WeekendRide->getLabel());
        $this->assertSame('After work', EventType::AfterWork->getLabel());
        $this->assertSame('Rookie ride', EventType::RookieRide->getLabel());
        $this->assertSame('Entraînement', EventType::Entrainement->getLabel());
        $this->assertSame('Projet spécial', EventType::ProjetSpecial->getLabel());
        $this->assertSame('Atelier', EventType::Atelier->getLabel());
        $this->assertSame('Skills clinic', EventType::SkillsClinic->getLabel());
        $this->assertSame('Réunion', EventType::Reunion->getLabel());
        $this->assertSame('Course', EventType::Course->getLabel());
    }

    public function test_get_color_returns_hex_values(): void
    {
        foreach (EventType::cases() as $case) {
            $this->assertMatchesRegularExpression('/^#[0-9a-fA-F]{6}$/', $case->getColor());
        }
    }
}
