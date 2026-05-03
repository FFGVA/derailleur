<?php

namespace Tests\Unit\Enums;

use App\Enums\MemberStatus;
use Filament\Support\Colors\Color;
use PHPUnit\Framework\TestCase;

class MemberStatusTest extends TestCase
{
    public function test_all_cases_exist(): void
    {
        $cases = MemberStatus::cases();

        $this->assertCount(6, $cases);
        $this->assertNotNull(MemberStatus::from('D'));
        $this->assertNotNull(MemberStatus::from('A'));
        $this->assertNotNull(MemberStatus::from('I'));
        $this->assertNotNull(MemberStatus::from('P'));
        $this->assertNotNull(MemberStatus::from('N'));
        $this->assertNotNull(MemberStatus::from('E'));
    }

    public function test_case_values(): void
    {
        $this->assertSame('D', MemberStatus::Brouillon->value);
        $this->assertSame('A', MemberStatus::Actif->value);
        $this->assertSame('I', MemberStatus::Inactif->value);
        $this->assertSame('P', MemberStatus::EnAttente->value);
        $this->assertSame('N', MemberStatus::NonMembre->value);
        $this->assertSame('E', MemberStatus::Enfant->value);
    }

    public function test_get_label_returns_french_labels(): void
    {
        $this->assertSame('Brouillon', MemberStatus::Brouillon->getLabel());
        $this->assertSame('Active', MemberStatus::Actif->getLabel());
        $this->assertSame('Inactive', MemberStatus::Inactif->getLabel());
        $this->assertSame('En attente', MemberStatus::EnAttente->getLabel());
        $this->assertSame('Non-membre', MemberStatus::NonMembre->getLabel());
        $this->assertSame('Active (mineure)', MemberStatus::Enfant->getLabel());
    }

    public function test_get_color_returns_valid_values(): void
    {
        $this->assertSame('gray', MemberStatus::Brouillon->getColor());
        $this->assertSame(Color::Green, MemberStatus::Actif->getColor());
        $this->assertSame('danger', MemberStatus::Inactif->getColor());
        $this->assertSame('warning', MemberStatus::EnAttente->getColor());
        $this->assertSame(Color::Cyan, MemberStatus::NonMembre->getColor());
        $this->assertSame(Color::Green, MemberStatus::Enfant->getColor());
    }
}
