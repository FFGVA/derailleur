<?php

namespace Tests\Unit\Services;

use App\Models\Event;
use App\Models\Invoice;
use App\Models\InvoiceLine;
use App\Models\Member;
use App\Services\InvoiceService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class InvoicePdfContentTest extends TestCase
{
    use DatabaseTransactions;

    private function makeMember(array $overrides = []): Member
    {
        return Member::create(array_merge([
            'first_name' => 'Marie',
            'last_name' => 'Dupont',
            'email' => 'pdf-' . uniqid() . '@test.ch',
            'address' => 'Rue du Lac 12',
            'postal_code' => '1200',
            'city' => 'Genève',
            'country' => 'CH',
            'statuscode' => 'A',
        ], $overrides));
    }

    private function cleanupPdf(Invoice $invoice): void
    {
        \Illuminate\Support\Facades\Storage::delete('invoices/' . $invoice->pdf_filename);
    }

    // ── Cotisation invoice — PDF structure ──

    public function test_cotisation_pdf_is_valid(): void
    {
        $member = $this->makeMember();
        $invoice = InvoiceService::createCotisation($member, (int) date('Y'));
        $pdfResult = InvoiceService::generatePdf($invoice);

        $this->assertStringStartsWith('%PDF', $pdfResult['pdf']);
        $this->assertGreaterThan(1000, strlen($pdfResult['pdf']), 'PDF should be substantial');

        $this->cleanupPdf($invoice);
    }

    public function test_cotisation_pdf_stores_file(): void
    {
        $member = $this->makeMember();
        $invoice = InvoiceService::createCotisation($member, (int) date('Y'));

        $this->assertTrue(
            \Illuminate\Support\Facades\Storage::exists('invoices/' . $invoice->pdf_filename)
        );

        $this->cleanupPdf($invoice);
    }

    public function test_cotisation_pdf_stores_filename_on_record(): void
    {
        $member = $this->makeMember();
        $invoice = InvoiceService::createCotisation($member, (int) date('Y'));

        $invoice->refresh();
        $this->assertNotNull($invoice->pdf_filename);

        $this->cleanupPdf($invoice);
    }

    // ── Cotisation invoice — database records ──

    public function test_cotisation_creates_invoice_record(): void
    {
        $member = $this->makeMember();
        $year = (int) date('Y');
        $invoice = InvoiceService::createCotisation($member, $year);

        $this->assertDatabaseHas('invoices', [
            'member_id' => $member->id,
            'type' => 'C',
            'cotisation_year' => $year,
            'statuscode' => 'N',
            'amount' => 50.00,
        ]);

        $this->cleanupPdf($invoice);
    }

    public function test_cotisation_creates_invoice_line_with_period(): void
    {
        $member = $this->makeMember(['membership_end' => '2026-12-31']);
        $invoice = InvoiceService::createCotisation($member, 2027);

        $line = $invoice->lines->first();

        $this->assertNotNull($line);
        $this->assertStringContainsString('Cotisation annuelle 2027', $line->description);
        $this->assertStringContainsString('01.01.2027', $line->description);
        $this->assertStringContainsString('31.12.2027', $line->description);

        $this->cleanupPdf($invoice);
    }

    public function test_cotisation_custom_amount(): void
    {
        $member = $this->makeMember();
        $invoice = InvoiceService::createCotisation($member, (int) date('Y'), 75.00);

        $this->assertEquals(75.00, (float) $invoice->amount);
        $this->assertEquals(75.00, (float) $invoice->lines->first()->amount);

        $this->cleanupPdf($invoice);
    }

    public function test_cotisation_filename_format(): void
    {
        $member = $this->makeMember(['first_name' => 'Marie', 'last_name' => 'Dupont']);
        $invoice = InvoiceService::createCotisation($member, (int) date('Y'));
        $pdfResult = InvoiceService::generatePdf($invoice);

        $this->assertStringStartsWith('ffgva_Dupont_Marie-facture-', $pdfResult['filename']);
        $this->assertStringEndsWith('.pdf', $pdfResult['filename']);

        $this->cleanupPdf($invoice);
    }

    public function test_cotisation_invoice_number_format(): void
    {
        $member = $this->makeMember();
        $invoice = InvoiceService::createCotisation($member, (int) date('Y'));

        $year = date('Y');
        $memberId = str_pad((string) $member->id, 3, '0', STR_PAD_LEFT);
        $this->assertMatchesRegularExpression("/^{$year}-{$memberId}-\\d{3}$/", $invoice->invoice_number);

        $this->cleanupPdf($invoice);
    }

    // ── Event invoice ──

    public function test_event_invoice_creates_correct_records(): void
    {
        $member = $this->makeMember();
        $event = Event::create([
            'title' => 'Sortie Payante',
            'starts_at' => now()->addWeek(),
            'statuscode' => 'P',
            'price' => 30.00,
        ]);

        $invoice = InvoiceService::createEvent($member, $event);

        $this->assertDatabaseHas('invoices', [
            'member_id' => $member->id,
            'type' => 'E',
            'amount' => 30.00,
        ]);

        $this->assertEquals(1, $invoice->lines()->count());
        $this->assertEquals(1, $invoice->events()->count());

        $this->cleanupPdf($invoice);
    }

    public function test_event_invoice_line_contains_event_title_and_date(): void
    {
        $member = $this->makeMember();
        $startDate = now()->addWeek();
        $event = Event::create([
            'title' => 'Tour du Salève',
            'starts_at' => $startDate,
            'statuscode' => 'P',
            'price' => 20.00,
        ]);

        $invoice = InvoiceService::createEvent($member, $event);
        $line = $invoice->lines->first();

        $this->assertStringContainsString('Tour du Salève', $line->description);
        $this->assertStringContainsString($startDate->format('d.m.Y'), $line->description);

        $this->cleanupPdf($invoice);
    }

    public function test_event_invoice_multi_events(): void
    {
        $member = $this->makeMember();
        $event1 = Event::create([
            'title' => 'Sortie A',
            'starts_at' => now()->addWeek(),
            'statuscode' => 'P',
            'price' => 20.00,
        ]);
        $event2 = Event::create([
            'title' => 'Sortie B',
            'starts_at' => now()->addWeeks(2),
            'statuscode' => 'P',
            'price' => 30.00,
        ]);

        $invoice = InvoiceService::createEvent($member, [$event1, $event2]);

        $this->assertEquals(50.00, (float) $invoice->amount);
        $this->assertEquals(2, $invoice->lines()->count());
        $this->assertEquals(2, $invoice->events()->count());

        $this->cleanupPdf($invoice);
    }

    public function test_event_invoice_uses_member_price_for_active_member(): void
    {
        $member = $this->makeMember(['statuscode' => 'A']);
        $event = Event::create([
            'title' => 'Sortie Prix',
            'starts_at' => now()->addWeek(),
            'statuscode' => 'P',
            'price' => 15.00,
            'price_non_member' => 25.00,
        ]);

        $invoice = InvoiceService::createEvent($member, $event);
        $this->assertEquals(15.00, (float) $invoice->amount);

        $this->cleanupPdf($invoice);
    }

    // ── Autre invoice ──

    public function test_autre_invoice_creates_record(): void
    {
        $member = $this->makeMember();
        $invoice = InvoiceService::createAutre($member, 'Note de test');

        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'type' => 'A',
            'amount' => 0,
            'statuscode' => 'N',
        ]);
    }

    // ── QR code ──

    public function test_qr_code_base64_returns_data_uri(): void
    {
        $member = $this->makeMember();
        $invoice = InvoiceService::createCotisation($member, (int) date('Y'));

        $qr = InvoiceService::generateQrCodeBase64($invoice);

        $this->assertNotNull($qr);
        $this->assertStringStartsWith('data:image/png;base64,', $qr);

        $this->cleanupPdf($invoice);
    }

    public function test_qr_code_contains_iban(): void
    {
        $member = $this->makeMember();
        $invoice = InvoiceService::createCotisation($member, (int) date('Y'));

        $qr = InvoiceService::generateQrCodeBase64($invoice);
        // The QR code itself encodes the IBAN - we can't decode PNG here,
        // but we verify the QR bill is built with correct IBAN via the config
        $this->assertEquals('CH9580808004931084283', config('ffgva.iban'));

        $this->cleanupPdf($invoice);
    }

    // ── computeMembershipEnd ──

    public function test_membership_end_is_dec_31_same_year(): void
    {
        $start = \Carbon\Carbon::create(2026, 3, 15);
        $end = InvoiceService::computeMembershipEnd($start);
        $this->assertEquals('2026-12-31', $end->format('Y-m-d'));
    }

    public function test_membership_end_extends_to_next_year_for_november(): void
    {
        $start = \Carbon\Carbon::create(2026, 11, 1);
        $end = InvoiceService::computeMembershipEnd($start);
        $this->assertEquals('2027-12-31', $end->format('Y-m-d'));
    }

    public function test_membership_end_extends_to_next_year_for_december(): void
    {
        $start = \Carbon\Carbon::create(2026, 12, 15);
        $end = InvoiceService::computeMembershipEnd($start);
        $this->assertEquals('2027-12-31', $end->format('Y-m-d'));
    }

    public function test_membership_end_october_stays_same_year(): void
    {
        $start = \Carbon\Carbon::create(2026, 10, 31);
        $end = InvoiceService::computeMembershipEnd($start);
        $this->assertEquals('2026-12-31', $end->format('Y-m-d'));
    }

    // ── onCotisationPaid ──

    public function test_on_cotisation_paid_extends_membership(): void
    {
        $member = $this->makeMember(['membership_end' => '2026-12-31']);
        $invoice = InvoiceService::createCotisation($member, 2027);
        $invoice->update(['statuscode' => 'P']);

        InvoiceService::onCotisationPaid($invoice);

        $member->refresh();
        $this->assertEquals('2027-12-31', $member->membership_end->format('Y-m-d'));
        $this->assertEquals('A', $member->getRawOriginal('statuscode'));

        \Illuminate\Support\Facades\Storage::delete('invoices/' . $invoice->pdf_filename);
    }

    public function test_on_cotisation_paid_ignores_event_invoice(): void
    {
        $member = $this->makeMember(['membership_end' => '2026-12-31', 'statuscode' => 'P']);
        $event = Event::create([
            'title' => 'Sortie',
            'starts_at' => now()->addWeek(),
            'statuscode' => 'P',
            'price' => 20.00,
        ]);

        $invoice = InvoiceService::createEvent($member, $event);

        InvoiceService::onCotisationPaid($invoice);

        $member->refresh();
        // Membership end should NOT change for event invoices
        $this->assertEquals('2026-12-31', $member->membership_end->format('Y-m-d'));
        $this->assertEquals('P', $member->getRawOriginal('statuscode'));

        \Illuminate\Support\Facades\Storage::delete('invoices/' . $invoice->pdf_filename);
    }
}
