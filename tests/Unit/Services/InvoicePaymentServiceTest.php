<?php

namespace Tests\Unit\Services;

use App\Enums\MemberStatus;
use App\Mail\ActivationMail;
use App\Models\Invoice;
use App\Models\Member;
use App\Services\InvoicePaymentService;
use App\Services\InvoiceService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class InvoicePaymentServiceTest extends TestCase
{
    use DatabaseTransactions;

    private function makeMember(array $overrides = []): Member
    {
        return Member::create(array_merge([
            'first_name' => 'Marie',
            'last_name' => 'Dupont',
            'email' => 'payment-svc-' . uniqid() . '@test.ch',
            'statuscode' => 'N',
            'membership_requested_at' => now(),
            'address' => 'Rue Test 1',
            'postal_code' => '1200',
            'city' => 'Genève',
        ], $overrides));
    }

    public function test_on_cotisation_paid_activates_member(): void
    {
        Mail::fake();

        $member = $this->makeMember();
        $invoice = InvoiceService::createCotisation($member, (int) date('Y'));
        $invoice->update(['statuscode' => 'P', 'payment_date' => now()]);

        InvoicePaymentService::onCotisationPaid($invoice);

        $member->refresh();
        $this->assertEquals(MemberStatus::Actif->value, $member->getRawOriginal('statuscode'));
    }

    public function test_on_cotisation_paid_clears_membership_requested_at(): void
    {
        Mail::fake();

        $member = $this->makeMember();
        $invoice = InvoiceService::createCotisation($member, (int) date('Y'));
        $invoice->update(['statuscode' => 'P', 'payment_date' => now()]);

        InvoicePaymentService::onCotisationPaid($invoice);

        $member->refresh();
        $this->assertNull($member->membership_requested_at);
    }

    public function test_on_cotisation_paid_sets_membership_end(): void
    {
        Mail::fake();

        $member = $this->makeMember();
        $invoice = InvoiceService::createCotisation($member, (int) date('Y'));
        $invoice->update(['statuscode' => 'P', 'payment_date' => now()]);

        InvoicePaymentService::onCotisationPaid($invoice);

        $member->refresh();
        $this->assertNotNull($member->membership_end);
    }

    public function test_on_cotisation_paid_sends_activation_email_for_new_member(): void
    {
        Mail::fake();

        $member = $this->makeMember();
        $invoice = InvoiceService::createCotisation($member, (int) date('Y'));
        $invoice->update(['statuscode' => 'P', 'payment_date' => now()]);

        InvoicePaymentService::onCotisationPaid($invoice);

        Mail::assertSent(ActivationMail::class);
    }

    public function test_on_cotisation_paid_no_activation_email_for_renewal(): void
    {
        Mail::fake();

        $member = $this->makeMember(['statuscode' => 'A', 'membership_end' => '2026-03-31']);
        $invoice = InvoiceService::createCotisation($member, (int) date('Y'));
        $invoice->update(['statuscode' => 'P', 'payment_date' => now()]);

        InvoicePaymentService::onCotisationPaid($invoice);

        Mail::assertNotSent(ActivationMail::class);
    }

    public function test_on_cotisation_paid_ignores_event_invoice(): void
    {
        Mail::fake();

        $member = $this->makeMember();
        $event = \App\Models\Event::create([
            'title' => 'Sortie',
            'starts_at' => now()->addWeek(),
            'statuscode' => 'P',
            'price' => 20,
        ]);
        $invoice = InvoiceService::createEvent($member, $event);

        InvoicePaymentService::onCotisationPaid($invoice);

        $member->refresh();
        $this->assertEquals(MemberStatus::NonMembre->value, $member->getRawOriginal('statuscode'));
    }
}
