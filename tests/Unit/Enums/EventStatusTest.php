<?php

namespace Tests\Unit\Enums;

use App\Enums\EventStatus;
use PHPUnit\Framework\TestCase;

class EventStatusTest extends TestCase
{
    public function test_all_cases_exist(): void
    {
        $cases = EventStatus::cases();

        $this->assertCount(4, $cases);
        $this->assertNotNull(EventStatus::from('N'));
        $this->assertNotNull(EventStatus::from('P'));
        $this->assertNotNull(EventStatus::from('X'));
        $this->assertNotNull(EventStatus::from('T'));
    }

    public function test_case_values(): void
    {
        $this->assertSame('N', EventStatus::Nouveau->value);
        $this->assertSame('P', EventStatus::Publie->value);
        $this->assertSame('X', EventStatus::Annule->value);
        $this->assertSame('T', EventStatus::Termine->value);
    }

    public function test_get_label_returns_french_labels(): void
    {
        $this->assertSame('Nouveau', EventStatus::Nouveau->getLabel());
        $this->assertSame('Publié', EventStatus::Publie->getLabel());
        $this->assertSame('Annulé', EventStatus::Annule->getLabel());
        $this->assertSame('Terminé', EventStatus::Termine->getLabel());
    }

    public function test_get_color_returns_valid_values(): void
    {
        $this->assertSame('info', EventStatus::Nouveau->getColor());
        $this->assertSame('success', EventStatus::Publie->getColor());
        $this->assertSame('danger', EventStatus::Annule->getColor());
        $this->assertSame('gray', EventStatus::Termine->getColor());
    }
}
