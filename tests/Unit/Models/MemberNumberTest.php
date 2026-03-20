<?php

namespace Tests\Unit\Models;

use App\Models\Member;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class MemberNumberTest extends TestCase
{
    use DatabaseTransactions;

    private function makeMember(array $overrides = []): Member
    {
        return Member::create(array_merge([
            'first_name' => 'Test',
            'last_name' => 'Number',
            'email' => 'memnum-' . uniqid() . '@test.ch',
        ], $overrides));
    }

    public function test_member_number_is_nullable(): void
    {
        $member = $this->makeMember();
        $this->assertNull($member->member_number);
    }

    public function test_member_number_is_fillable(): void
    {
        $member = $this->makeMember(['member_number' => '0042']);
        $this->assertEquals('0042', $member->member_number);
    }

    public function test_assign_member_number_uses_high_watermark(): void
    {
        // Create a member with an existing number
        $this->makeMember(['member_number' => '0010']);

        $member = $this->makeMember();
        $number = Member::assignMemberNumber($member);

        $this->assertEquals('0011', $number);
        $this->assertEquals('0011', $member->fresh()->member_number);
    }

    public function test_assign_member_number_starts_at_0001(): void
    {
        // No existing member numbers in this transaction
        // Clear all member numbers first
        Member::query()->update(['member_number' => null]);

        $member = $this->makeMember();
        $number = Member::assignMemberNumber($member);

        $this->assertEquals('0001', $number);
    }

    public function test_assign_member_number_skips_already_assigned(): void
    {
        $member = $this->makeMember(['member_number' => '0005']);
        $result = Member::assignMemberNumber($member);

        // Should return existing number, not assign a new one
        $this->assertEquals('0005', $result);
    }

    public function test_invoice_number_uses_member_id_not_member_number(): void
    {
        $member = $this->makeMember(['member_number' => '0042']);
        $invoiceNumber = \App\Models\Invoice::generateNumber($member);

        $memberId = str_pad((string) $member->id, 3, '0', STR_PAD_LEFT);
        $year = date('Y');
        $this->assertEquals("{$year}-{$memberId}-001", $invoiceNumber);
    }
}
