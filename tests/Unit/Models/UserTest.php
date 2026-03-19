<?php

namespace Tests\Unit\Models;

use App\Enums\UserRole;
use App\Models\Member;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class UserTest extends TestCase
{
    use DatabaseTransactions;

    public function test_is_admin_returns_true_when_role_is_a(): void
    {
        $user = User::create([
            'name' => 'Admin User',
            'email' => 'admin-test-' . uniqid() . '@example.com',
            'password' => 'password123',
            'role' => 'A',
        ]);

        $this->assertTrue($user->isAdmin());
        $this->assertFalse($user->isChefPeloton());
    }

    public function test_is_chef_peloton_returns_true_when_role_is_c(): void
    {
        $user = User::create([
            'name' => 'Chef User',
            'email' => 'chef-test-' . uniqid() . '@example.com',
            'password' => 'password123',
            'role' => 'C',
        ]);

        $this->assertTrue($user->isChefPeloton());
        $this->assertFalse($user->isAdmin());
    }

    public function test_role_casts_to_user_role_enum(): void
    {
        $user = User::create([
            'name' => 'Role Test',
            'email' => 'role-test-' . uniqid() . '@example.com',
            'password' => 'password123',
            'role' => 'A',
        ]);

        $user->refresh();

        $this->assertInstanceOf(UserRole::class, $user->role);
        $this->assertSame(UserRole::Admin, $user->role);
    }

    public function test_member_relationship_returns_belongs_to(): void
    {
        $member = Member::create([
            'first_name' => 'User',
            'last_name' => 'Member',
            'email' => 'usermember-' . uniqid() . '@example.com',
            'statuscode' => 'A',
        ]);

        $user = User::create([
            'name' => 'Linked User',
            'email' => 'linked-test-' . uniqid() . '@example.com',
            'password' => 'password123',
            'role' => 'C',
            'member_id' => $member->id,
        ]);

        $this->assertInstanceOf(BelongsTo::class, $user->member());
        $this->assertInstanceOf(Member::class, $user->member);
        $this->assertSame($member->id, $user->member->id);
    }
}
