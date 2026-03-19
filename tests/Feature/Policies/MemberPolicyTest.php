<?php

namespace Tests\Feature\Policies;

use App\Models\Member;
use App\Models\User;
use App\Policies\MemberPolicy;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class MemberPolicyTest extends TestCase
{
    use DatabaseTransactions;

    private MemberPolicy $policy;
    private User $admin;
    private User $chef;
    private Member $member;

    protected function setUp(): void
    {
        parent::setUp();

        $this->policy = new MemberPolicy;

        $this->member = Member::create([
            'first_name' => 'Test',
            'last_name' => 'Member',
            'email' => 'policy-member-' . uniqid() . '@example.com',
            'is_invitee' => false,
            'statuscode' => 'A',
        ]);

        $this->admin = User::factory()->create([
            'role' => 'A',
            'email' => 'policy-admin-' . uniqid() . '@example.com',
        ]);

        $this->chef = User::factory()->create([
            'role' => 'C',
            'member_id' => $this->member->id,
            'email' => 'policy-chef-' . uniqid() . '@example.com',
        ]);
    }

    public function test_admin_can_view_any_members(): void
    {
        $this->assertTrue($this->policy->viewAny($this->admin));
    }

    public function test_admin_can_view_member(): void
    {
        $this->assertTrue($this->policy->view($this->admin, $this->member));
    }

    public function test_admin_can_create_member(): void
    {
        $this->assertTrue($this->policy->create($this->admin));
    }

    public function test_admin_can_update_member(): void
    {
        $this->assertTrue($this->policy->update($this->admin, $this->member));
    }

    public function test_admin_can_delete_member(): void
    {
        $this->assertTrue($this->policy->delete($this->admin, $this->member));
    }

    public function test_chef_can_view_any_members(): void
    {
        $this->assertTrue($this->policy->viewAny($this->chef));
    }

    public function test_chef_can_view_member(): void
    {
        $this->assertTrue($this->policy->view($this->chef, $this->member));
    }

    public function test_chef_can_update_member(): void
    {
        $this->assertTrue($this->policy->update($this->chef, $this->member));
    }

    public function test_chef_cannot_create_member(): void
    {
        $this->assertFalse($this->policy->create($this->chef));
    }

    public function test_chef_cannot_delete_member(): void
    {
        $this->assertFalse($this->policy->delete($this->chef, $this->member));
    }
}
