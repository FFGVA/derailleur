<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Models\MemberMagicToken;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class MemberMagicTokenTest extends TestCase
{
    use DatabaseTransactions;

    private function createActiveMember(): Member
    {
        return Member::create([
            'first_name' => 'Test',
            'last_name' => 'Token',
            'email' => 'token-test-' . uniqid() . '@example.com',
            'statuscode' => 'A',
            'is_invitee' => false,
        ]);
    }

    public function test_generate_for_creates_token_in_database(): void
    {
        $member = $this->createActiveMember();

        [$model, $rawToken] = MemberMagicToken::generateFor($member);

        $this->assertDatabaseHas('member_magic_tokens', [
            'id' => $model->id,
            'member_id' => $member->id,
        ]);
        $this->assertNotNull($rawToken);
        $this->assertEquals(64, strlen($rawToken));
    }

    public function test_find_by_raw_token_returns_model(): void
    {
        $member = $this->createActiveMember();
        [$model, $rawToken] = MemberMagicToken::generateFor($member);

        $found = MemberMagicToken::findByRawToken($rawToken);

        $this->assertNotNull($found);
        $this->assertEquals($model->id, $found->id);
    }

    public function test_find_by_raw_token_returns_null_for_invalid(): void
    {
        $found = MemberMagicToken::findByRawToken(str_repeat('a', 64));

        $this->assertNull($found);
    }

    public function test_is_valid_returns_true_for_fresh_token(): void
    {
        $member = $this->createActiveMember();
        [$model, $rawToken] = MemberMagicToken::generateFor($member);

        $this->assertTrue($model->isValid());
    }

    public function test_is_valid_returns_false_for_expired_token(): void
    {
        $member = $this->createActiveMember();
        [$model, $rawToken] = MemberMagicToken::generateFor($member);

        $model->update(['expires_at' => now()->subMinute()]);
        $model->refresh();

        $this->assertFalse($model->isValid());
    }

    public function test_is_valid_returns_false_for_used_token(): void
    {
        $member = $this->createActiveMember();
        [$model, $rawToken] = MemberMagicToken::generateFor($member);

        $model->markUsed();
        $model->refresh();

        $this->assertFalse($model->isValid());
    }

    public function test_mark_used_stamps_used_at(): void
    {
        $member = $this->createActiveMember();
        [$model, $rawToken] = MemberMagicToken::generateFor($member);

        $this->assertNull($model->used_at);

        $model->markUsed();
        $model->refresh();

        $this->assertNotNull($model->used_at);
    }
}
