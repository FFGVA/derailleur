<?php

namespace Tests\Browser;

use App\Models\Invoice;
use App\Models\Member;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class InvoiceCrudTest extends DuskTestCase
{
    public function test_invoice_list_page_loads(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsAdmin($browser);

            $browser->visit('/admin/invoices')
                ->waitForText('Factures')
                ->assertSee('Factures');
        });
    }

    public function test_invoice_list_shows_invoices(): void
    {
        $email = 'dusk-inv-' . uniqid() . '@test.ch';
        $this->cleanupMemberEmails[] = $email;

        $member = Member::create([
            'first_name' => 'DuskFacture',
            'last_name' => 'Test',
            'email' => $email,
            'statuscode' => 'A',
            'address' => 'Rue Test 1',
            'postal_code' => '1200',
            'city' => 'Genève',
            'country' => 'CH',
        ]);

        $invoice = Invoice::create([
            'member_id' => $member->id,
            'invoice_number' => Invoice::generateNumber($member),
            'amount' => 50.00,
            'statuscode' => 'N',
            'type' => 'C',
            'cotisation_year' => (int) date('Y'),
        ]);

        $this->browse(function (Browser $browser) use ($invoice) {
            $this->loginAsAdmin($browser);

            $browser->visit('/admin/invoices')
                ->waitForText($invoice->invoice_number)
                ->assertSee($invoice->invoice_number)
                ->assertSee('50.00');
        });
    }

    public function test_view_invoice(): void
    {
        $email = 'dusk-inv-view-' . uniqid() . '@test.ch';
        $this->cleanupMemberEmails[] = $email;

        $member = Member::create([
            'first_name' => 'DuskView',
            'last_name' => 'Invoice',
            'email' => $email,
            'statuscode' => 'A',
            'address' => 'Rue Test 1',
            'postal_code' => '1200',
            'city' => 'Genève',
            'country' => 'CH',
        ]);

        $invoice = Invoice::create([
            'member_id' => $member->id,
            'invoice_number' => Invoice::generateNumber($member),
            'amount' => 50.00,
            'statuscode' => 'E',
            'type' => 'C',
            'cotisation_year' => (int) date('Y'),
        ]);

        $this->browse(function (Browser $browser) use ($invoice) {
            $this->loginAsAdmin($browser);

            $browser->visit('/admin/invoices/' . $invoice->id)
                ->waitForText($invoice->invoice_number)
                ->assertSee($invoice->invoice_number)
                ->assertSee('DuskView');
        });
    }

    public function test_invoice_shows_status_badge(): void
    {
        $email = 'dusk-inv-badge-' . uniqid() . '@test.ch';
        $this->cleanupMemberEmails[] = $email;

        $member = Member::create([
            'first_name' => 'DuskBadge',
            'last_name' => 'Test',
            'email' => $email,
            'statuscode' => 'A',
            'address' => 'Rue Test 1',
            'postal_code' => '1200',
            'city' => 'Genève',
            'country' => 'CH',
        ]);

        Invoice::create([
            'member_id' => $member->id,
            'invoice_number' => Invoice::generateNumber($member),
            'amount' => 50.00,
            'statuscode' => 'E',
            'type' => 'C',
            'cotisation_year' => (int) date('Y'),
        ]);

        $this->browse(function (Browser $browser) {
            $this->loginAsAdmin($browser);

            $browser->visit('/admin/invoices')
                ->waitForText('DuskBadge')
                ->assertSee('DuskBadge')
                // Status badge for 'E' (Envoyée) should be visible
                ->assertSee('Envoyée');
        });
    }

    public function test_cotisations_page_loads(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsAdmin($browser);

            $browser->visit('/admin/cotisations')
                ->waitForText('Cotisations')
                ->assertSee('Cotisations');
        });
    }
}
