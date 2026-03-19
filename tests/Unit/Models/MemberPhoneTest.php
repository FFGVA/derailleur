<?php

namespace Tests\Unit\Models;

use App\Models\Member;
use App\Models\MemberPhone;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class MemberPhoneTest extends TestCase
{
    use DatabaseTransactions;

    private function makeMember(): Member
    {
        return Member::create([
            'first_name' => 'Phone',
            'last_name' => 'Test',
            'email' => 'phone-' . uniqid() . '@example.com',
            'statuscode' => 'A',
        ]);
    }

    private function makePhone(Member $member, array $overrides = []): MemberPhone
    {
        return MemberPhone::create(array_merge([
            'member_id' => $member->id,
            'phone_number' => '+41791234567',
            'label' => 'Mobile',
        ], $overrides));
    }

    public function test_can_create_phone(): void
    {
        $member = $this->makeMember();
        $phone = $this->makePhone($member, [
            'is_whatsapp' => true,
            'sort_order' => 1,
        ]);

        $this->assertDatabaseHas('member_phones', [
            'id' => $phone->id,
            'phone_number' => '+41791234567',
            'label' => 'Mobile',
        ]);
    }

    public function test_is_whatsapp_casts_to_boolean(): void
    {
        $member = $this->makeMember();
        $phone = $this->makePhone($member, ['is_whatsapp' => 1]);
        $phone->refresh();

        $this->assertIsBool($phone->is_whatsapp);
        $this->assertTrue($phone->is_whatsapp);
    }

    public function test_member_relationship_returns_belongs_to(): void
    {
        $member = $this->makeMember();
        $phone = $this->makePhone($member, ['label' => 'Home']);

        $this->assertInstanceOf(BelongsTo::class, $phone->member());
        $this->assertInstanceOf(Member::class, $phone->member);
        $this->assertSame($member->id, $phone->member->id);
    }

    public function test_soft_delete_works(): void
    {
        $member = $this->makeMember();
        $phone = $this->makePhone($member, ['label' => 'Work']);
        $phoneId = $phone->id;
        $phone->delete();

        $this->assertSoftDeleted('member_phones', ['id' => $phoneId]);
        $this->assertNull(MemberPhone::find($phoneId));
        $this->assertNotNull(MemberPhone::withTrashed()->find($phoneId));
    }

    public function test_no_created_at_column(): void
    {
        $this->assertNull(MemberPhone::CREATED_AT);

        $member = $this->makeMember();
        $phone = $this->makePhone($member, ['label' => 'Other']);

        $this->assertNull($phone->created_at);
    }
}
