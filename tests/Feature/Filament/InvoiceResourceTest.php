<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\InvoiceResource\Pages\ListInvoices;
use App\Models\Invoice;
use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Tests\TestCase;

class InvoiceResourceTest extends TestCase
{
    use DatabaseTransactions;

    private function makeAdmin(): User
    {
        return User::create([
            'name' => 'Admin',
            'email' => 'inv-admin-' . uniqid() . '@test.ch',
            'password' => bcrypt('password'),
            'role' => 'A',
        ]);
    }

    private function makeMember(): Member
    {
        return Member::create([
            'first_name' => 'Test',
            'last_name' => 'Facture',
            'email' => 'inv-mem-' . uniqid() . '@test.ch',
        ]);
    }

    public function test_invoice_list_page_loads(): void
    {
        $this->actingAs($this->makeAdmin())
            ->get('/admin/invoices')
            ->assertStatus(200);
    }

    public function test_invoice_list_shows_invoices(): void
    {
        $member = $this->makeMember();
        $number = Invoice::generateNumber($member);
        Invoice::create([
            'member_id' => $member->id,
            'invoice_number' => $number,
            'amount' => 50.00,
            'statuscode' => 'N',
        ]);

        $this->actingAs($this->makeAdmin())
            ->get('/admin/invoices')
            ->assertSee($number);
    }

    public function test_invoice_navigation_exists(): void
    {
        $response = $this->actingAs($this->makeAdmin())
            ->get('/admin/invoices');

        $response->assertStatus(200);
        $response->assertSee('Factures');
    }

    public function test_mark_paid_accepts_valid_date(): void
    {
        $member = $this->makeMember();
        $invoice = Invoice::create([
            'member_id' => $member->id,
            'invoice_number' => Invoice::generateNumber($member),
            'amount' => 50.00,
            'statuscode' => 'E',
        ]);

        Livewire::actingAs($this->makeAdmin())
            ->test(ListInvoices::class)
            ->callTableAction('markPaid', $invoice, data: [
                'payment_date' => '15.03.2026',
            ])
            ->assertHasNoTableActionErrors();

        $invoice->refresh();
        $this->assertEquals('P', $invoice->getRawOriginal('statuscode'));
        $this->assertEquals('2026-03-15', $invoice->payment_date->format('Y-m-d'));
    }

    public function test_mark_paid_rejects_invalid_date(): void
    {
        $member = $this->makeMember();
        $invoice = Invoice::create([
            'member_id' => $member->id,
            'invoice_number' => Invoice::generateNumber($member),
            'amount' => 50.00,
            'statuscode' => 'E',
        ]);

        Livewire::actingAs($this->makeAdmin())
            ->test(ListInvoices::class)
            ->callTableAction('markPaid', $invoice, data: [
                'payment_date' => 'not-a-date',
            ])
            ->assertHasTableActionErrors(['payment_date']);
    }

    public function test_mark_paid_rejects_impossible_date(): void
    {
        $member = $this->makeMember();
        $invoice = Invoice::create([
            'member_id' => $member->id,
            'invoice_number' => Invoice::generateNumber($member),
            'amount' => 50.00,
            'statuscode' => 'E',
        ]);

        Livewire::actingAs($this->makeAdmin())
            ->test(ListInvoices::class)
            ->callTableAction('markPaid', $invoice, data: [
                'payment_date' => '31.02.2026',
            ])
            ->assertHasTableActionErrors(['payment_date']);
    }

    public function test_mark_paid_rejects_empty_date(): void
    {
        $member = $this->makeMember();
        $invoice = Invoice::create([
            'member_id' => $member->id,
            'invoice_number' => Invoice::generateNumber($member),
            'amount' => 50.00,
            'statuscode' => 'E',
        ]);

        Livewire::actingAs($this->makeAdmin())
            ->test(ListInvoices::class)
            ->callTableAction('markPaid', $invoice, data: [
                'payment_date' => '',
            ])
            ->assertHasTableActionErrors(['payment_date']);
    }
}
