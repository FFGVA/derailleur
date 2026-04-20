<?php

namespace Tests\Unit\Filament;

use App\Filament\Forms\PaymentDateForm;
use Tests\TestCase;

class PaymentDateFormTest extends TestCase
{
    public function test_schema_returns_array_of_components(): void
    {
        $schema = PaymentDateForm::schema();

        $this->assertIsArray($schema);
        $this->assertNotEmpty($schema);
    }

    public function test_schema_includes_payment_date_in_grid(): void
    {
        $schema = PaymentDateForm::schema();

        $grid = $schema[0];
        $this->assertInstanceOf(\Filament\Forms\Components\Grid::class, $grid);

        $children = $grid->getChildComponents();
        $this->assertNotEmpty($children);
        $this->assertEquals('payment_date', $children[0]->getName());
    }

    public function test_schema_includes_notes_textarea(): void
    {
        $schema = PaymentDateForm::schema();

        $found = false;
        foreach ($schema as $component) {
            if ($component instanceof \Filament\Forms\Components\Textarea && $component->getName() === 'notes') {
                $found = true;
            }
        }
        $this->assertTrue($found, 'notes textarea not found in schema');
    }
}
