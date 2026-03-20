<?php

namespace Tests\Unit\Models;

use App\Models\Invoice;
use App\Models\Member;
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

    public function test_can_create_invoice(): void
    {
        $member = $this->makeMember();
        $invoice = Invoice::create([
            'member_id' => $member->id,
            'invoice_number' => '2026-001-001',
            'amount' => 50.00,
            'statuscode' => 'N',
        ]);

        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'invoice_number' => '2026-001-001',
        ]);
    }

    public function test_member_relationship(): void
    {
        $member = $this->makeMember();
        $invoice = Invoice::create([
            'member_id' => $member->id,
            'invoice_number' => '2026-001-001',
            'amount' => 50.00,
            'statuscode' => 'N',
        ]);

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
        $invoice = Invoice::create([
            'member_id' => $member->id,
            'invoice_number' => '2026-001-001',
            'amount' => 50.00,
            'statuscode' => 'N',
        ]);
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
        Invoice::create([
            'member_id' => $member->id,
            'invoice_number' => $first,
            'amount' => 50.00,
            'statuscode' => 'N',
        ]);

        $second = Invoice::generateNumber($member);
        $this->assertNotEquals($first, $second);

        // Extract sequence numbers
        $seq1 = (int) substr($first, -3);
        $seq2 = (int) substr($second, -3);
        $this->assertEquals($seq1 + 1, $seq2);
    }

    public function test_statuscode_casts_to_enum(): void
    {
        $member = $this->makeMember();
        $invoice = Invoice::create([
            'member_id' => $member->id,
            'invoice_number' => '2026-001-001',
            'amount' => 50.00,
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
        $invoice = Invoice::create([
            'member_id' => $member->id,
            'invoice_number' => '2026-001-001',
            'amount' => 50.00,
            'statuscode' => 'N',
        ]);
        $invoice->refresh();

        $this->assertEquals('50.00', $invoice->amount);
    }
}
