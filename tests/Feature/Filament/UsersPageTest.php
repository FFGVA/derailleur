<?php

namespace Tests\Feature\Filament;

use App\Filament\Pages\Users;
use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Tests\TestCase;

class UsersPageTest extends TestCase
{
    use DatabaseTransactions;

    private function makeAdmin(): User
    {
        return User::create([
            'name' => 'Admin',
            'email' => 'up-admin-' . uniqid() . '@test.ch',
            'password' => bcrypt('password'),
            'role' => 'A',
        ]);
    }

    private function makeChef(): User
    {
        return User::create([
            'name' => 'Chef',
            'email' => 'up-chef-' . uniqid() . '@test.ch',
            'password' => bcrypt('password'),
            'role' => 'C',
        ]);
    }

    // ── Access control ──

    public function test_page_loads_for_admin(): void
    {
        $this->actingAs($this->makeAdmin())
            ->get('/admin/users')
            ->assertStatus(200);
    }

    public function test_page_forbidden_for_chef(): void
    {
        $this->actingAs($this->makeChef())
            ->get('/admin/users')
            ->assertStatus(403);
    }

    // ── Create user ──

    public function test_create_user(): void
    {
        $admin = $this->makeAdmin();
        $email = 'new-user-' . uniqid() . '@test.ch';

        Livewire::actingAs($admin)
            ->test(Users::class)
            ->callTableAction('create', data: [
                'name' => 'Nouveau',
                'email' => $email,
                'role' => 'C',
                'password' => 'securepass123',
            ])
            ->assertHasNoTableActionErrors()
            ->assertNotified();

        $this->assertDatabaseHas('users', [
            'email' => $email,
            'role' => 'C',
        ]);
    }

    public function test_create_requires_password(): void
    {
        Livewire::actingAs($this->makeAdmin())
            ->test(Users::class)
            ->callTableAction('create', data: [
                'name' => 'Nouveau',
                'email' => 'req-' . uniqid() . '@test.ch',
                'role' => 'C',
                'password' => '',
            ])
            ->assertHasTableActionErrors(['password']);
    }

    public function test_create_requires_unique_email(): void
    {
        $admin = $this->makeAdmin();

        Livewire::actingAs($admin)
            ->test(Users::class)
            ->callTableAction('create', data: [
                'name' => 'Doublon',
                'email' => $admin->email,
                'role' => 'C',
                'password' => 'securepass123',
            ])
            ->assertHasTableActionErrors(['email']);
    }

    public function test_create_password_min_length(): void
    {
        Livewire::actingAs($this->makeAdmin())
            ->test(Users::class)
            ->callTableAction('create', data: [
                'name' => 'Short',
                'email' => 'short-' . uniqid() . '@test.ch',
                'role' => 'C',
                'password' => 'abc',
            ])
            ->assertHasTableActionErrors(['password']);
    }

    // ── Edit user ──

    public function test_edit_user(): void
    {
        $admin = $this->makeAdmin();
        $target = User::create([
            'name' => 'Cible',
            'email' => 'up-target-' . uniqid() . '@test.ch',
            'password' => bcrypt('password'),
            'role' => 'C',
        ]);

        Livewire::actingAs($admin)
            ->test(Users::class)
            ->callTableAction('edit', $target, data: [
                'name' => 'Modifié',
                'email' => $target->email,
                'role' => 'A',
                'member_id' => null,
            ])
            ->assertNotified();

        $target->refresh();
        $this->assertEquals('Modifié', $target->name);
        $this->assertEquals('A', $target->getRawOriginal('role'));
    }

    public function test_edit_with_linked_member(): void
    {
        $admin = $this->makeAdmin();
        $member = Member::create([
            'first_name' => 'Liée',
            'last_name' => 'Membre',
            'email' => 'up-linked-' . uniqid() . '@test.ch',
            'statuscode' => 'A',
        ]);
        $target = User::create([
            'name' => 'Target',
            'email' => 'up-link-' . uniqid() . '@test.ch',
            'password' => bcrypt('password'),
            'role' => 'C',
        ]);

        Livewire::actingAs($admin)
            ->test(Users::class)
            ->callTableAction('edit', $target, data: [
                'name' => $target->name,
                'email' => $target->email,
                'role' => 'C',
                'member_id' => $member->id,
            ])
            ->assertNotified();

        $target->refresh();
        $this->assertEquals($member->id, $target->member_id);
    }

    // ── Lock user ──

    public function test_lock_user(): void
    {
        $admin = $this->makeAdmin();
        $target = User::create([
            'name' => 'Lockable',
            'email' => 'up-lock-' . uniqid() . '@test.ch',
            'password' => bcrypt('password'),
            'role' => 'C',
        ]);

        Livewire::actingAs($admin)
            ->test(Users::class)
            ->callTableAction('lock', $target)
            ->assertNotified();

        $target->refresh();
        $this->assertTrue($target->is_locked);
        $this->assertStringStartsWith('locked_', $target->email);
        $this->assertNull($target->remember_token);
    }

    public function test_lock_hidden_for_self(): void
    {
        $admin = $this->makeAdmin();

        Livewire::actingAs($admin)
            ->test(Users::class)
            ->assertTableActionHidden('lock', $admin);
    }

    public function test_lock_hidden_for_already_locked(): void
    {
        $admin = $this->makeAdmin();
        $locked = User::create([
            'name' => 'Locked',
            'email' => 'locked_999@disabled.local',
            'password' => bcrypt('password'),
            'role' => 'C',
            'is_locked' => true,
        ]);

        Livewire::actingAs($admin)
            ->test(Users::class)
            ->assertTableActionHidden('lock', $locked);
    }

    // ── Unlock user ──

    public function test_unlock_user(): void
    {
        $admin = $this->makeAdmin();
        $locked = User::create([
            'name' => 'Locked',
            'email' => 'locked_999@disabled.local',
            'password' => bcrypt('password'),
            'role' => 'C',
            'is_locked' => true,
        ]);

        $newEmail = 'unlocked-' . uniqid() . '@test.ch';

        Livewire::actingAs($admin)
            ->test(Users::class)
            ->callTableAction('unlock', $locked, data: [
                'email' => $newEmail,
            ])
            ->assertHasNoTableActionErrors()
            ->assertNotified();

        $locked->refresh();
        $this->assertFalse($locked->is_locked);
        $this->assertEquals($newEmail, $locked->email);
    }

    public function test_unlock_hidden_for_non_locked(): void
    {
        $admin = $this->makeAdmin();
        $target = User::create([
            'name' => 'Normal',
            'email' => 'up-normal-' . uniqid() . '@test.ch',
            'password' => bcrypt('password'),
            'role' => 'C',
        ]);

        Livewire::actingAs($admin)
            ->test(Users::class)
            ->assertTableActionHidden('unlock', $target);
    }

    public function test_unlock_requires_unique_email(): void
    {
        $admin = $this->makeAdmin();
        $locked = User::create([
            'name' => 'Locked',
            'email' => 'locked_888@disabled.local',
            'password' => bcrypt('password'),
            'role' => 'C',
            'is_locked' => true,
        ]);

        Livewire::actingAs($admin)
            ->test(Users::class)
            ->callTableAction('unlock', $locked, data: [
                'email' => $admin->email,
            ])
            ->assertHasTableActionErrors(['email']);
    }
}
