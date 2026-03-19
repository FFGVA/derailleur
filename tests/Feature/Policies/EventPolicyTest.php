<?php

namespace Tests\Feature\Policies;

use App\Models\Event;
use App\Models\Member;
use App\Models\User;
use App\Policies\EventPolicy;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class EventPolicyTest extends TestCase
{
    use DatabaseTransactions;

    private EventPolicy $policy;
    private User $admin;
    private User $chef;
    private Member $chefMember;
    private Member $otherMember;
    private Event $ownEvent;
    private Event $otherEvent;

    protected function setUp(): void
    {
        parent::setUp();

        $this->policy = new EventPolicy;

        $this->chefMember = Member::create([
            'first_name' => 'Chef',
            'last_name' => 'Peloton',
            'email' => 'event-chef-member-' . uniqid() . '@example.com',
            'is_invitee' => false,
            'statuscode' => 'A',
        ]);

        $this->otherMember = Member::create([
            'first_name' => 'Other',
            'last_name' => 'Chef',
            'email' => 'event-other-member-' . uniqid() . '@example.com',
            'is_invitee' => false,
            'statuscode' => 'A',
        ]);

        $this->admin = User::factory()->create([
            'role' => 'A',
            'email' => 'event-admin-' . uniqid() . '@example.com',
        ]);

        $this->chef = User::factory()->create([
            'role' => 'C',
            'member_id' => $this->chefMember->id,
            'email' => 'event-chef-' . uniqid() . '@example.com',
        ]);

        $this->ownEvent = Event::create([
            'title' => 'Own Event',
            'starts_at' => now()->addDays(7),
            'chef_peloton_id' => $this->chefMember->id,
            'statuscode' => 'P',
        ]);

        $this->otherEvent = Event::create([
            'title' => 'Other Event',
            'starts_at' => now()->addDays(14),
            'chef_peloton_id' => $this->otherMember->id,
            'statuscode' => 'P',
        ]);
    }

    public function test_admin_can_view_any_events(): void
    {
        $this->assertTrue($this->policy->viewAny($this->admin));
    }

    public function test_admin_can_view_event(): void
    {
        $this->assertTrue($this->policy->view($this->admin, $this->ownEvent));
    }

    public function test_admin_can_create_event(): void
    {
        $this->assertTrue($this->policy->create($this->admin));
    }

    public function test_admin_can_update_any_event(): void
    {
        $this->assertTrue($this->policy->update($this->admin, $this->ownEvent));
        $this->assertTrue($this->policy->update($this->admin, $this->otherEvent));
    }

    public function test_admin_can_delete_event(): void
    {
        $this->assertTrue($this->policy->delete($this->admin, $this->ownEvent));
    }

    public function test_chef_can_update_own_event(): void
    {
        $this->assertTrue($this->policy->update($this->chef, $this->ownEvent));
    }

    public function test_chef_cannot_update_other_event(): void
    {
        $this->assertFalse($this->policy->update($this->chef, $this->otherEvent));
    }

    public function test_chef_cannot_create_event(): void
    {
        $this->assertFalse($this->policy->create($this->chef));
    }

    public function test_chef_cannot_delete_event(): void
    {
        $this->assertFalse($this->policy->delete($this->chef, $this->ownEvent));
    }
}
