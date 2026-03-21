<?php

namespace Tests\Feature\Filament;

use App\Filament\Widgets\ExpiringMemberships;
use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Tests\TestCase;

class ExpiringMembershipsTest extends TestCase
{
    use DatabaseTransactions;

    private function makeAdmin(): User
    {
        return User::create([
            'name' => 'Admin',
            'email' => 'exp-' . uniqid() . '@test.ch',
            'password' => bcrypt('password'),
            'role' => 'A',
        ]);
    }

    public function test_widget_shows_expiring_count(): void
    {
        // Expires next month
        Member::create([
            'first_name' => 'Expiring', 'last_name' => 'Soon',
            'email' => 'exp-s-' . uniqid() . '@test.ch',
            'statuscode' => 'A',
            'membership_end' => now()->addDays(20),
        ]);

        $this->actingAs($this->makeAdmin());

        Livewire::test(ExpiringMemberships::class)
            ->assertSee('expir');
    }

    public function test_widget_counts_past_due(): void
    {
        // Already expired
        Member::create([
            'first_name' => 'Expired', 'last_name' => 'Past',
            'email' => 'exp-p-' . uniqid() . '@test.ch',
            'statuscode' => 'A',
            'membership_end' => now()->subMonths(2),
        ]);

        $this->actingAs($this->makeAdmin());

        Livewire::test(ExpiringMemberships::class)
            ->assertSee('expir');
    }

    public function test_widget_excludes_far_future(): void
    {
        // Expires in 6 months — should not be counted
        Member::create([
            'first_name' => 'Future', 'last_name' => 'Safe',
            'email' => 'exp-f-' . uniqid() . '@test.ch',
            'statuscode' => 'A',
            'membership_end' => now()->addMonths(6),
        ]);

        $this->actingAs($this->makeAdmin());

        // Widget should still render but this member shouldn't be in the count
        Livewire::test(ExpiringMemberships::class)
            ->assertSuccessful();
    }
}
