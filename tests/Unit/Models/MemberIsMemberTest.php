<?php

namespace Tests\Unit\Models;

use App\Models\Member;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class MemberIsMemberTest extends TestCase
{
    use DatabaseTransactions;

    private function makeMember(string $statuscode): Member
    {
        return Member::create([
            'first_name' => 'Test',
            'last_name' => 'Member',
            'email' => 'isMember-' . uniqid() . '@example.com',
            'statuscode' => $statuscode,
        ]);
    }

    public function test_actif_is_member(): void
    {
        $this->assertTrue($this->makeMember('A')->isMember());
    }

    public function test_enfant_is_member(): void
    {
        $this->assertTrue($this->makeMember('E')->isMember());
    }

    public function test_non_membre_is_not_member(): void
    {
        $this->assertFalse($this->makeMember('N')->isMember());
    }

    public function test_en_attente_is_not_member(): void
    {
        $this->assertFalse($this->makeMember('P')->isMember());
    }

    public function test_inactif_is_not_member(): void
    {
        $this->assertFalse($this->makeMember('I')->isMember());
    }
}
