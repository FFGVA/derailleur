<?php

namespace Tests\Unit\Services;

use App\Enums\InvoiceStatus;
use App\Mail\InvoiceMail;
use App\Models\Event;
use App\Models\Invoice;
use App\Models\Member;
use App\Services\InvoiceEmailService;
use App\Services\InvoiceService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class InvoiceEmailServiceTest extends TestCase
{
    use DatabaseTransactions;

    private function makeMember(array $overrides = []): Member
    {
        return Member::create(array_merge([
            'first_name' => 'Marie',
            'last_name' => 'Dupont',
            'email' => 'invoice-email-' . uniqid() . '@test.ch',
            'statuscode' => 'A',
            'address' => 'Rue Test 1',
            'postal_code' => '1200',
            'city' => 'Genève',
        ], $overrides));
    }

    public function test_send_cotisation_creates_invoice_and_sends_email(): void
    {
        Mail::fake();

        $member = $this->makeMember();
        $invoice = InvoiceEmailService::createAndSendCotisation($member, (int) date('Y'));

        $this->assertInstanceOf(Invoice::class, $invoice);
        $this->assertEquals(InvoiceStatus::Sent->value, $invoice->getRawOriginal('statuscode'));
        Mail::assertSent(InvoiceMail::class, fn ($mail) => $mail->invoice->id === $invoice->id);
    }

    public function test_send_event_creates_invoice_and_sends_email_with_ical(): void
    {
        Mail::fake();

        $member = $this->makeMember();
        $event = Event::create([
            'title' => 'Sortie test',
            'starts_at' => '2026-05-01 09:00:00',
            'statuscode' => 'P',
            'price' => 20,
        ]);

        $invoice = InvoiceEmailService::createAndSendEvent($member, $event);

        $this->assertInstanceOf(Invoice::class, $invoice);
        $this->assertEquals(InvoiceStatus::Sent->value, $invoice->getRawOriginal('statuscode'));
        Mail::assertSent(InvoiceMail::class, fn ($mail) => $mail->invoice->id === $invoice->id);
    }

    public function test_send_existing_invoice_sends_email(): void
    {
        Mail::fake();

        $member = $this->makeMember();
        $invoice = InvoiceService::createCotisation($member, (int) date('Y'));

        InvoiceEmailService::sendExisting($invoice);

        $this->assertEquals(InvoiceStatus::Sent->value, $invoice->fresh()->getRawOriginal('statuscode'));
        Mail::assertSent(InvoiceMail::class);
    }
}
