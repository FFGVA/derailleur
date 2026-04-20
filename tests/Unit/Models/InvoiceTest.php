<?php

namespace Tests\Unit\Models;

use App\Models\Invoice;
use App\Models\Member;
use App\Services\InvoicePaymentService;
use App\Services\InvoiceService;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class InvoiceTest extends TestCase
{
    use DatabaseTransactions;

    private function makeMember(): Member
    {
        return Member::create([
            'first_name' => 'Invoice',
            'last_name' => 'Test',
            'email' => 'inv-' . uniqid() . '@test.ch',
        ]);
    }

    private function makeInvoice(Member $member, array $overrides = []): Invoice
    {
        return Invoice::create(array_merge([
            'member_id' => $member->id,
            'invoice_number' => Invoice::generateNumber($member),
            'amount' => 50.00,
            'statuscode' => 'N',
        ], $overrides));
    }

    public function test_can_create_invoice(): void
    {
        $member = $this->makeMember();
        $invoice = $this->makeInvoice($member);

        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
        ]);
    }

    public function test_member_relationship(): void
    {
        $member = $this->makeMember();
        $invoice = $this->makeInvoice($member);

        $this->assertInstanceOf(BelongsTo::class, $invoice->member());
        $this->assertEquals($member->id, $invoice->member->id);
    }

    public function test_no_created_at(): void
    {
        $this->assertNull(Invoice::CREATED_AT);
    }

    public function test_soft_delete(): void
    {
        $member = $this->makeMember();
        $invoice = $this->makeInvoice($member);
        $id = $invoice->id;
        $invoice->delete();

        $this->assertSoftDeleted('invoices', ['id' => $id]);
    }

    public function test_generate_invoice_number(): void
    {
        $member = $this->makeMember();

        $number = Invoice::generateNumber($member);

        $year = date('Y');
        $memberId = str_pad((string) $member->id, 3, '0', STR_PAD_LEFT);
        $this->assertMatchesRegularExpression("/^{$year}-{$memberId}-\d{3}$/", $number);
    }

    public function test_generate_invoice_number_increments(): void
    {
        $member = $this->makeMember();

        $first = Invoice::generateNumber($member);
        $this->makeInvoice($member, ['invoice_number' => $first]);

        $second = Invoice::generateNumber($member);
        $this->assertNotEquals($first, $second);

        $seq1 = (int) substr($first, -3);
        $seq2 = (int) substr($second, -3);
        $this->assertEquals($seq1 + 1, $seq2);
    }

    public function test_statuscode_casts_to_enum(): void
    {
        $member = $this->makeMember();
        $invoice = $this->makeInvoice($member, [
            'statuscode' => 'P',
            'payment_date' => now(),
        ]);
        $invoice->refresh();

        $this->assertInstanceOf(\App\Enums\InvoiceStatus::class, $invoice->statuscode);
        $this->assertEquals(\App\Enums\InvoiceStatus::Paid, $invoice->statuscode);
    }

    public function test_amount_casts_to_decimal(): void
    {
        $member = $this->makeMember();
        $invoice = $this->makeInvoice($member);
        $invoice->refresh();

        $this->assertEquals('50.00', $invoice->amount);
    }

    public function test_cotisation_invoice_line_shows_next_period(): void
    {
        $member = Member::create([
            'first_name' => 'Period',
            'last_name' => 'Test',
            'email' => 'period-' . uniqid() . '@test.ch',
            'statuscode' => 'A',
            'membership_start' => '2025-01-01',
            'membership_end' => '2026-03-31',
            'address' => 'Rue Test 1',
            'postal_code' => '1200',
            'city' => 'Genève',
        ]);

        $invoice = InvoiceService::createCotisation($member, 2026);
        $line = $invoice->lines->first();

        // Next period starts day after current membership_end, ends 31.12 of that year
        $this->assertStringContainsString('01.04.2026', $line->description);
        $this->assertStringContainsString('31.12.2026', $line->description);
    }

    public function test_cotisation_invoice_line_defaults_next_period_when_no_end(): void
    {
        $member = Member::create([
            'first_name' => 'NoEnd',
            'last_name' => 'Test',
            'email' => 'noend-' . uniqid() . '@test.ch',
            'statuscode' => 'A',
            'address' => 'Rue Test 1',
            'postal_code' => '1200',
            'city' => 'Genève',
        ]);

        $invoice = InvoiceService::createCotisation($member, 2026);
        $line = $invoice->lines->first();

        // When no membership_end, start from today, end 31.12 of current year
        // (unless Nov/Dec, then end of next year)
        $expectedStart = now()->format('d.m.Y');
        $expectedEnd = InvoicePaymentService::computeMembershipEnd(now())->format('d.m.Y');
        $this->assertStringContainsString($expectedStart, $line->description);
        $this->assertStringContainsString($expectedEnd, $line->description);
    }

    public function test_mark_paid_updates_member_membership_end(): void
    {
        $member = Member::create([
            'first_name' => 'Paid',
            'last_name' => 'Test',
            'email' => 'paid-' . uniqid() . '@test.ch',
            'statuscode' => 'A',
            'membership_start' => '2025-01-01',
            'membership_end' => '2026-03-31',
            'address' => 'Rue Test 1',
            'postal_code' => '1200',
            'city' => 'Genève',
        ]);

        $invoice = InvoiceService::createCotisation($member, 2026);

        $invoice->update([
            'statuscode' => 'P',
            'payment_date' => now(),
        ]);

        // Simulate what markPaid should do
        InvoicePaymentService::onCotisationPaid($invoice);

        $member->refresh();
        // membership_end was 2026-03-31, next period starts 2026-04-01, ends 31.12.2026
        $this->assertEquals('2026-12-31', $member->membership_end->format('Y-m-d'));
    }
}
