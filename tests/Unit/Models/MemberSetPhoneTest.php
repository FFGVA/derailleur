<?php

namespace Tests\Unit\Models;

use App\Models\Member;
use App\Models\MemberPhone;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class MemberSetPhoneTest extends TestCase
{
    use DatabaseTransactions;

    public function test_set_phone_creates_when_none_exists(): void
    {
        $member = Member::create([
            'first_name' => 'Test',
            'last_name' => 'Phone',
            'email' => 'setphone-' . uniqid() . '@test.ch',
            'statuscode' => 'A',
        ]);

        $member->setPhone('+41 79 123 45 67');

        $this->assertEquals(1, $member->phones()->count());
        $this->assertEquals('+41 79 123 45 67', $member->phones()->first()->phone_number);
        $this->assertEquals('Mobile principal', $member->phones()->first()->label);
    }

    public function test_set_phone_updates_existing(): void
    {
        $member = Member::create([
            'first_name' => 'Test',
            'last_name' => 'Phone',
            'email' => 'setphone-' . uniqid() . '@test.ch',
            'statuscode' => 'A',
        ]);

        MemberPhone::create([
            'member_id' => $member->id,
            'phone_number' => '+41 79 111 11 11',
            'label' => 'Mobile principal',
        ]);

        $member->setPhone('+41 79 222 22 22');

        $this->assertEquals(1, $member->phones()->count());
        $this->assertEquals('+41 79 222 22 22', $member->phones()->first()->phone_number);
    }

    public function test_set_phone_with_custom_label(): void
    {
        $member = Member::create([
            'first_name' => 'Test',
            'last_name' => 'Phone',
            'email' => 'setphone-' . uniqid() . '@test.ch',
            'statuscode' => 'A',
        ]);

        $member->setPhone('+41 79 123 45 67', 'Travail');

        $this->assertEquals('Travail', $member->phones()->first()->label);
    }
}
