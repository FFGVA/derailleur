<?php

namespace Tests\Unit\Services;

use App\Models\Member;
use App\Services\InvoiceService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class InvoiceServiceTest extends TestCase
{
    use DatabaseTransactions;

    private function makeMember(): Member
    {
        return Member::create([
            'first_name' => 'Marie',
            'last_name' => 'Dupont',
            'email' => 'inv-svc-' . uniqid() . '@test.ch',
            'address' => 'Rue du Lac 12',
            'postal_code' => '1200',
            'city' => 'Genève',
            'country' => 'CH',
        ]);
    }

    public function test_generates_pdf_content(): void
    {
        $member = $this->makeMember();
        $result = InvoiceService::generate($member);

        $this->assertArrayHasKey('pdf', $result);
        $this->assertNotEmpty($result['pdf']);
        $this->assertStringStartsWith('%PDF', $result['pdf']);
    }

    public function test_returns_filename(): void
    {
        $member = $this->makeMember();
        $result = InvoiceService::generate($member);

        $this->assertArrayHasKey('filename', $result);
        $this->assertStringStartsWith('ffgva_Dupont_Marie-facture-', $result['filename']);
        $this->assertStringEndsWith('.pdf', $result['filename']);
    }

    public function test_returns_invoice_number(): void
    {
        $member = $this->makeMember();
        $result = InvoiceService::generate($member);

        $this->assertArrayHasKey('invoice_number', $result);
        $year = date('Y');
        $memberId = str_pad((string) $member->id, 3, '0', STR_PAD_LEFT);
        $this->assertMatchesRegularExpression("/^{$year}-{$memberId}-001$/", $result['invoice_number']);
    }

    public function test_creates_invoice_record(): void
    {
        $member = $this->makeMember();
        InvoiceService::generate($member);

        $this->assertDatabaseHas('invoices', [
            'member_id' => $member->id,
            'amount' => 50.00,
            'statuscode' => 'N',
        ]);
    }

    public function test_filename_handles_spaces_in_names(): void
    {
        $member = Member::create([
            'first_name' => 'Marie Claire',
            'last_name' => 'De La Tour',
            'email' => 'inv-svc-' . uniqid() . '@test.ch',
        ]);
        $result = InvoiceService::generate($member);

        $this->assertStringStartsWith('ffgva_De_La_Tour_Marie_Claire-facture-', $result['filename']);
    }
}
