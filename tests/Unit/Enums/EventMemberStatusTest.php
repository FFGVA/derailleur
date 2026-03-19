<?php

namespace Tests\Unit\Enums;

use App\Enums\EventMemberStatus;
use PHPUnit\Framework\TestCase;

class EventMemberStatusTest extends TestCase
{
    public function test_all_cases_exist(): void
    {
        $cases = EventMemberStatus::cases();

        $this->assertCount(3, $cases);
        $this->assertNotNull(EventMemberStatus::from('N'));
        $this->assertNotNull(EventMemberStatus::from('C'));
        $this->assertNotNull(EventMemberStatus::from('X'));
    }

    public function test_case_values(): void
    {
        $this->assertSame('N', EventMemberStatus::Inscrit->value);
        $this->assertSame('C', EventMemberStatus::Confirme->value);
        $this->assertSame('X', EventMemberStatus::Annule->value);
    }

    public function test_get_label_returns_french_labels(): void
    {
        $this->assertSame('Inscrit', EventMemberStatus::Inscrit->getLabel());
        $this->assertSame('Confirmé', EventMemberStatus::Confirme->getLabel());
        $this->assertSame('Annulé', EventMemberStatus::Annule->getLabel());
    }

    public function test_get_color_returns_valid_values(): void
    {
        $this->assertSame('warning', EventMemberStatus::Inscrit->getColor());
        $this->assertSame('success', EventMemberStatus::Confirme->getColor());
        $this->assertSame('danger', EventMemberStatus::Annule->getColor());
    }
}
