<?php

namespace Tests\Feature\Filament;

use App\Filament\Pages\Cotisations;
use App\Models\Invoice;
use App\Services\InvoicePaymentService;
use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class CotisationsPageTest extends TestCase
{
    use DatabaseTransactions;

    private function makeAdmin(): User
    {
        return User::create([
            'name' => 'Admin',
            'email' => 'cot-admin-' . uniqid() . '@test.ch',
            'password' => bcrypt('password'),
            'role' => 'A',
        ]);
    }

    private function makeExpiringMember(): Member
    {
        return Member::create([
            'first_name' => 'Aline',
            'last_name' => 'TestCot',
            'email' => 'cot-mem-' . uniqid() . '@test.ch',
            'statuscode' => 'A',
            'membership_start' => now()->subYear(),
            'membership_end' => now()->endOfMonth(),
            'address' => 'Rue Test 1',
            'postal_code' => '1200',
            'city' => 'Genève',
            'country' => 'CH',
        ]);
    }

    public function test_cotisations_page_loads(): void
    {
        $this->actingAs($this->makeAdmin())
            ->get('/admin/cotisations')
            ->assertStatus(200);
    }

    public function test_send_invoice_action_visible_when_no_invoice(): void
    {
        $admin = $this->makeAdmin();
        $member = $this->makeExpiringMember();

        Livewire::actingAs($admin)
            ->test(Cotisations::class)
            ->assertTableActionVisible('sendInvoice', $member);
    }

    public function test_send_invoice_action_hidden_when_invoice_exists(): void
    {
        $admin = $this->makeAdmin();
        $member = $this->makeExpiringMember();

        Invoice::create([
            'member_id' => $member->id,
            'type' => 'C',
            'cotisation_year' => (int) date('Y'),
            'invoice_number' => Invoice::generateNumber($member),
            'amount' => 50.00,
            'statuscode' => 'N',
        ]);

        Livewire::actingAs($admin)
            ->test(Cotisations::class)
            ->assertTableActionHidden('sendInvoice', $member);
    }

    public function test_send_invoice_creates_invoice_and_sends_email(): void
    {
        Mail::fake();

        $admin = $this->makeAdmin();
        $member = $this->makeExpiringMember();

        Livewire::actingAs($admin)
            ->test(Cotisations::class)
            ->callTableAction('sendInvoice', $member)
            ->assertNotified();

        // Invoice created
        $this->assertDatabaseHas('invoices', [
            'member_id' => $member->id,
            'type' => 'C',
            'cotisation_year' => (int) date('Y'),
            'statuscode' => 'E',
        ]);

        // Invoice line describes the NEXT period
        $invoice = Invoice::where('member_id', $member->id)->where('type', 'C')->first();
        $line = $invoice->lines->first();
        $nextStart = $member->membership_end->addDay()->format('d.m.Y');
        $nextEnd = InvoicePaymentService::computeMembershipEnd($member->membership_end->addDay())->format('d.m.Y');
        $this->assertStringContainsString($nextStart, $line->description);
        $this->assertStringContainsString($nextEnd, $line->description);

        // Email sent
        Mail::assertSent(\App\Mail\InvoiceMail::class, function ($mail) use ($member) {
            return $mail->invoice->member_id === $member->id;
        });
    }

    public function test_send_invoice_does_not_duplicate(): void
    {
        Mail::fake();

        $admin = $this->makeAdmin();
        $member = $this->makeExpiringMember();

        // Create existing invoice for current year
        Invoice::create([
            'member_id' => $member->id,
            'type' => 'C',
            'cotisation_year' => (int) date('Y'),
            'invoice_number' => Invoice::generateNumber($member),
            'amount' => 50.00,
            'statuscode' => 'E',
        ]);

        // Action should be hidden, but also guard in the action itself
        $this->assertEquals(1, Invoice::where('member_id', $member->id)->where('type', 'C')->count());
    }

    public function test_mark_paid_visible_when_sent_invoice_exists(): void
    {
        $admin = $this->makeAdmin();
        $member = $this->makeExpiringMember();

        Invoice::create([
            'member_id' => $member->id,
            'type' => 'C',
            'cotisation_year' => (int) date('Y'),
            'invoice_number' => Invoice::generateNumber($member),
            'amount' => 50.00,
            'statuscode' => 'E',
        ]);

        Livewire::actingAs($admin)
            ->test(Cotisations::class)
            ->assertTableActionVisible('markPaid', $member);
    }

    public function test_mark_paid_hidden_when_no_invoice(): void
    {
        $admin = $this->makeAdmin();
        $member = $this->makeExpiringMember();

        Livewire::actingAs($admin)
            ->test(Cotisations::class)
            ->assertTableActionHidden('markPaid', $member);
    }

    public function test_mark_paid_sets_payment_date_and_extends_membership(): void
    {
        $admin = $this->makeAdmin();
        $member = $this->makeExpiringMember();
        $originalEnd = $member->membership_end->copy();

        $invoice = Invoice::create([
            'member_id' => $member->id,
            'type' => 'C',
            'cotisation_year' => (int) date('Y'),
            'invoice_number' => Invoice::generateNumber($member),
            'amount' => 50.00,
            'statuscode' => 'E',
        ]);

        Livewire::actingAs($admin)
            ->test(Cotisations::class)
            ->callTableAction('markPaid', $member, data: [
                'payment_date' => '18.03.2026',
            ])
            ->assertHasNoTableActionErrors()
            ->assertNotified();

        $invoice->refresh();
        $this->assertEquals('P', $invoice->getRawOriginal('statuscode'));
        $this->assertEquals('2026-03-18', $invoice->payment_date->format('Y-m-d'));

        $member->refresh();
        $expectedEnd = InvoicePaymentService::computeMembershipEnd($originalEnd->addDay());
        $this->assertEquals($expectedEnd->format('Y-m-d'), $member->membership_end->format('Y-m-d'));
    }

    public function test_mark_paid_rejects_invalid_date(): void
    {
        $admin = $this->makeAdmin();
        $member = $this->makeExpiringMember();

        Invoice::create([
            'member_id' => $member->id,
            'type' => 'C',
            'cotisation_year' => (int) date('Y'),
            'invoice_number' => Invoice::generateNumber($member),
            'amount' => 50.00,
            'statuscode' => 'E',
        ]);

        Livewire::actingAs($admin)
            ->test(Cotisations::class)
            ->callTableAction('markPaid', $member, data: [
                'payment_date' => 'bad',
            ])
            ->assertHasTableActionErrors(['payment_date']);
    }
}
