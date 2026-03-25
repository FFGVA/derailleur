<?php

namespace Tests\Unit\Models;

use App\Models\Event;
use App\Models\Member;
use App\Models\MemberPhone;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ModifiedByTest extends TestCase
{
    use DatabaseTransactions;

    private function makeUser(): User
    {
        return User::create([
            'name' => 'Test User',
            'email' => 'modifiedby-' . uniqid() . '@test.ch',
            'password' => bcrypt('password'),
            'role' => 'A',
        ]);
    }

    private function makeMember(array $overrides = []): Member
    {
        return Member::create(array_merge([
            'first_name' => 'Test',
            'last_name' => 'Member',
            'email' => 'modby-' . uniqid() . '@test.ch',
        ], $overrides));
    }

    public function test_member_has_modified_by_relationship(): void
    {
        $user = $this->makeUser();
        $member = $this->makeMember(['modified_by_id' => $user->id]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $member->modifiedBy());
        $this->assertEquals($user->id, $member->modifiedBy->id);
    }

    public function test_event_has_modified_by_relationship(): void
    {
        $user = $this->makeUser();
        $event = Event::create([
            'title' => 'Test Event',
            'starts_at' => now(),
            'modified_by_id' => $user->id,
        ]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $event->modifiedBy());
        $this->assertEquals($user->id, $event->modifiedBy->id);
    }

    public function test_member_phone_has_modified_by_relationship(): void
    {
        $user = $this->makeUser();
        $member = $this->makeMember();
        $phone = MemberPhone::create([
            'member_id' => $member->id,
            'phone_number' => '+41791234567',
            'modified_by_id' => $user->id,
        ]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $phone->modifiedBy());
        $this->assertEquals($user->id, $phone->modifiedBy->id);
    }

    public function test_modified_by_is_nullable(): void
    {
        $member = $this->makeMember(['modified_by_id' => null]);
        $this->assertNull($member->modified_by_id);
    }

    public function test_modified_by_id_is_fillable_on_member(): void
    {
        $user = $this->makeUser();
        $member = $this->makeMember(['modified_by_id' => $user->id]);
        $this->assertEquals($user->id, $member->modified_by_id);
    }

    public function test_modified_by_id_is_fillable_on_event(): void
    {
        $user = $this->makeUser();
        $event = Event::create([
            'title' => 'Test',
            'starts_at' => now(),
            'modified_by_id' => $user->id,
        ]);
        $this->assertEquals($user->id, $event->modified_by_id);
    }

    // ── SetsModifiedBy trait ──

    public function test_member_auto_sets_modified_by_on_create(): void
    {
        $user = $this->makeUser();
        $this->actingAs($user);

        $member = $this->makeMember();

        $this->assertEquals($user->id, $member->modified_by_id);
    }

    public function test_member_auto_sets_modified_by_on_update(): void
    {
        $member = $this->makeMember();
        $this->assertNull($member->modified_by_id);

        $user = $this->makeUser();
        $this->actingAs($user);

        $member->update(['first_name' => 'Updated']);

        $this->assertEquals($user->id, $member->modified_by_id);
    }

    public function test_event_auto_sets_modified_by_on_create(): void
    {
        $user = $this->makeUser();
        $this->actingAs($user);

        $event = Event::create([
            'title' => 'Auto Test',
            'starts_at' => now(),
            'statuscode' => 'N',
        ]);

        $this->assertEquals($user->id, $event->modified_by_id);
    }

    public function test_phone_auto_sets_modified_by_on_create(): void
    {
        $user = $this->makeUser();
        $this->actingAs($user);

        $member = $this->makeMember();
        $phone = MemberPhone::create([
            'member_id' => $member->id,
            'phone_number' => '+41 79 000 00 00',
        ]);

        $this->assertEquals($user->id, $phone->modified_by_id);
    }

    public function test_invoice_auto_sets_modified_by_on_create(): void
    {
        $user = $this->makeUser();
        $this->actingAs($user);

        $member = $this->makeMember();
        $invoice = \App\Models\Invoice::create([
            'member_id' => $member->id,
            'invoice_number' => \App\Models\Invoice::generateNumber($member),
            'amount' => 50.00,
            'statuscode' => 'N',
        ]);

        $this->assertEquals($user->id, $invoice->modified_by_id);
    }

    public function test_modified_by_not_set_when_no_auth(): void
    {
        $member = $this->makeMember();

        $this->assertNull($member->modified_by_id);
    }
}
