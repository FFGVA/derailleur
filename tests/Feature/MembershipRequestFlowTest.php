<?php

namespace Tests\Feature;

use App\Mail\AdhesionConfirmationMail;
use App\Mail\AdhesionMail;
use App\Mail\AdhesionWelcomeMail;
use App\Mail\ActivationMail;
use App\Mail\InvoiceMail;
use App\Models\Invoice;
use App\Models\Member;
use App\Services\InvoiceService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tests\TestCase;

class MembershipRequestFlowTest extends TestCase
{
    use DatabaseTransactions;

    private array $headers = [
        'Origin' => 'https://ffgva.ch',
        'Content-Type' => 'application/json',
    ];

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'nom' => 'Dupont',
            'prenom' => 'Marie',
            'email' => 'flow-test-' . uniqid() . '@example.com',
            'telephone' => '+41 79 123 45 67',
            'photo_ok' => 'oui',
            'type_velo' => 'route',
            'sorties' => 'weekend',
            'atelier' => 'non',
            'instagram' => '',
            'strava' => '',
            'statuts_ok' => 'oui',
            'cotisation_ok' => 'oui',
        ], $overrides);
    }

    // ── New member (unknown email) ──

    public function test_new_member_gets_status_p(): void
    {
        Mail::fake();

        $payload = $this->validPayload();
        $this->postJson('/api/adhesion', $payload, $this->headers);

        $member = Member::where('email', $payload['email'])->first();
        $this->assertEquals('P', $member->getRawOriginal('statuscode'));
        $this->assertNull($member->membership_requested_at);
    }

    public function test_new_member_activation_sets_status_n_and_date(): void
    {
        Mail::fake();

        $rawToken = Str::random(64);
        $member = Member::create([
            'first_name' => 'Nouvelle',
            'last_name' => 'Membre',
            'email' => 'activation-flow-' . uniqid() . '@example.com',
            'statuscode' => 'P',
            'activation_token' => Hash::make($rawToken),
            'activation_sent_at' => now(),
            'address' => 'Rue Test 1',
            'postal_code' => '1200',
            'city' => 'Genève',
        ]);

        $this->get('/adhesion/confirmer?' . http_build_query([
            'token' => $rawToken,
            'email' => $member->email,
        ]));

        $member->refresh();
        $this->assertEquals('N', $member->getRawOriginal('statuscode'));
        $this->assertNotNull($member->membership_requested_at);
    }

    public function test_new_member_activation_sends_invoice(): void
    {
        Mail::fake();

        $rawToken = Str::random(64);
        $member = Member::create([
            'first_name' => 'Nouvelle',
            'last_name' => 'Membre',
            'email' => 'invoice-flow-' . uniqid() . '@example.com',
            'statuscode' => 'P',
            'activation_token' => Hash::make($rawToken),
            'activation_sent_at' => now(),
            'address' => 'Rue Test 1',
            'postal_code' => '1200',
            'city' => 'Genève',
        ]);

        $this->get('/adhesion/confirmer?' . http_build_query([
            'token' => $rawToken,
            'email' => $member->email,
        ]));

        Mail::assertSent(InvoiceMail::class);
    }

    // ── Existing non-member requesting membership ──

    public function test_existing_non_member_stays_n_with_date_set(): void
    {
        Mail::fake();

        $email = 'nonmember-flow-' . uniqid() . '@example.com';
        Member::create([
            'first_name' => 'Existante',
            'last_name' => 'NonMembre',
            'email' => $email,
            'statuscode' => 'N',
        ]);

        $this->postJson('/api/adhesion', $this->validPayload(['email' => $email]), $this->headers);

        $member = Member::where('email', $email)->first();
        $this->assertEquals('N', $member->getRawOriginal('statuscode'));
        $this->assertNotNull($member->membership_requested_at);
    }

    public function test_existing_non_member_no_activation_email(): void
    {
        Mail::fake();

        $email = 'nonmember-nomail-' . uniqid() . '@example.com';
        Member::create([
            'first_name' => 'Existante',
            'last_name' => 'NonMembre',
            'email' => $email,
            'statuscode' => 'N',
        ]);

        $this->postJson('/api/adhesion', $this->validPayload(['email' => $email]), $this->headers);

        Mail::assertNotSent(AdhesionWelcomeMail::class);
    }

    public function test_existing_non_member_gets_invoice_directly(): void
    {
        Mail::fake();

        $email = 'nonmember-invoice-' . uniqid() . '@example.com';
        Member::create([
            'first_name' => 'Existante',
            'last_name' => 'NonMembre',
            'email' => $email,
            'statuscode' => 'N',
            'address' => 'Rue Test 1',
            'postal_code' => '1200',
            'city' => 'Genève',
        ]);

        $this->postJson('/api/adhesion', $this->validPayload(['email' => $email]), $this->headers);

        Mail::assertSent(InvoiceMail::class);
    }

    // ── Payment flow ──

    public function test_payment_clears_membership_requested_at(): void
    {
        Mail::fake();

        $member = Member::create([
            'first_name' => 'Payante',
            'last_name' => 'Membre',
            'email' => 'payment-flow-' . uniqid() . '@example.com',
            'statuscode' => 'N',
            'membership_requested_at' => now(),
            'address' => 'Rue Test 1',
            'postal_code' => '1200',
            'city' => 'Genève',
        ]);

        $result = InvoiceService::createCotisation($member, (int) date('Y'));
        $invoice = Invoice::where('invoice_number', $result['invoice_number'])->first();
        $invoice->update(['statuscode' => 'P', 'payment_date' => now()]);

        InvoiceService::onCotisationPaid($invoice);

        $member->refresh();
        $this->assertEquals('A', $member->getRawOriginal('statuscode'));
        $this->assertNull($member->membership_requested_at);
    }

    public function test_payment_sends_activation_email_for_new_member(): void
    {
        Mail::fake();

        $member = Member::create([
            'first_name' => 'Nouvelle',
            'last_name' => 'Payante',
            'email' => 'activation-pay-' . uniqid() . '@example.com',
            'statuscode' => 'N',
            'membership_requested_at' => now(),
            'address' => 'Rue Test 1',
            'postal_code' => '1200',
            'city' => 'Genève',
        ]);

        $result = InvoiceService::createCotisation($member, (int) date('Y'));
        $invoice = Invoice::where('invoice_number', $result['invoice_number'])->first();
        $invoice->update(['statuscode' => 'P', 'payment_date' => now()]);

        InvoiceService::onCotisationPaid($invoice);

        Mail::assertSent(ActivationMail::class);
    }

    public function test_renewal_does_not_send_activation_email(): void
    {
        Mail::fake();

        $member = Member::create([
            'first_name' => 'Renouvellement',
            'last_name' => 'Membre',
            'email' => 'renewal-' . uniqid() . '@example.com',
            'statuscode' => 'A',
            'membership_end' => '2026-03-31',
            'address' => 'Rue Test 1',
            'postal_code' => '1200',
            'city' => 'Genève',
        ]);

        $result = InvoiceService::createCotisation($member, (int) date('Y'));
        $invoice = Invoice::where('invoice_number', $result['invoice_number'])->first();
        $invoice->update(['statuscode' => 'P', 'payment_date' => now()]);

        InvoiceService::onCotisationPaid($invoice);

        Mail::assertNotSent(ActivationMail::class);
    }
}
