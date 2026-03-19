<?php

namespace Tests\Unit\Enums;

use App\Enums\MemberStatus;
use PHPUnit\Framework\TestCase;

class MemberStatusTest extends TestCase
{
    public function test_all_cases_exist(): void
    {
        $cases = MemberStatus::cases();

        $this->assertCount(4, $cases);
        $this->assertNotNull(MemberStatus::from('D'));
        $this->assertNotNull(MemberStatus::from('A'));
        $this->assertNotNull(MemberStatus::from('I'));
        $this->assertNotNull(MemberStatus::from('P'));
    }

    public function test_case_values(): void
    {
        $this->assertSame('D', MemberStatus::Brouillon->value);
        $this->assertSame('A', MemberStatus::Actif->value);
        $this->assertSame('I', MemberStatus::Inactif->value);
        $this->assertSame('P', MemberStatus::EnAttente->value);
    }

    public function test_get_label_returns_french_labels(): void
    {
        $this->assertSame('Brouillon', MemberStatus::Brouillon->getLabel());
        $this->assertSame('Actif', MemberStatus::Actif->getLabel());
        $this->assertSame('Inactif', MemberStatus::Inactif->getLabel());
        $this->assertSame('En attente', MemberStatus::EnAttente->getLabel());
    }

    public function test_get_color_returns_valid_values(): void
    {
        $this->assertSame('gray', MemberStatus::Brouillon->getColor());
        $this->assertSame('success', MemberStatus::Actif->getColor());
        $this->assertSame('danger', MemberStatus::Inactif->getColor());
        $this->assertSame('warning', MemberStatus::EnAttente->getColor());
    }
}
