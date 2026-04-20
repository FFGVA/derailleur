<?php

namespace Tests\Unit\Enums;

use App\Enums\InvoiceType;
use PHPUnit\Framework\TestCase;

class InvoiceTypeTest extends TestCase
{
    public function test_all_cases_exist(): void
    {
        $this->assertCount(3, InvoiceType::cases());
        $this->assertNotNull(InvoiceType::from('C'));
        $this->assertNotNull(InvoiceType::from('E'));
        $this->assertNotNull(InvoiceType::from('A'));
    }

    public function test_get_label_returns_french_labels(): void
    {
        $this->assertSame('Cotisation', InvoiceType::Cotisation->getLabel());
        $this->assertSame('Événement', InvoiceType::Evenement->getLabel());
        $this->assertSame('Autre', InvoiceType::Autre->getLabel());
    }

    public function test_get_color_returns_valid_values(): void
    {
        $this->assertSame('primary', InvoiceType::Cotisation->getColor());
        $this->assertSame('info', InvoiceType::Evenement->getColor());
        $this->assertSame('gray', InvoiceType::Autre->getColor());
    }
}
