<?php

namespace Tests\Feature\Filament;

use App\Filament\Widgets\StatsOverview;
use App\Models\Invoice;
use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Tests\TestCase;

class DashboardWidgetsTest extends TestCase
{
    use DatabaseTransactions;

    private function makeAdmin(): User
    {
        return User::create([
            'name' => 'Admin',
            'email' => 'dash-' . uniqid() . '@test.ch',
            'password' => bcrypt('password'),
            'role' => 'A',
        ]);
    }

    public function test_dashboard_loads(): void
    {
        $this->actingAs($this->makeAdmin())
            ->get('/admin')
            ->assertStatus(200);
    }

    public function test_stats_widget_shows_unpaid_total(): void
    {
        $member = Member::create([
            'first_name' => 'Test', 'last_name' => 'Dash',
            'email' => 'dash-m-' . uniqid() . '@test.ch', 'statuscode' => 'A',
        ]);
        Invoice::create(['member_id' => $member->id, 'invoice_number' => 'T-001', 'amount' => 50.00, 'statuscode' => 'N']);
        Invoice::create(['member_id' => $member->id, 'invoice_number' => 'T-002', 'amount' => 50.00, 'statuscode' => 'E']);
        Invoice::create(['member_id' => $member->id, 'invoice_number' => 'T-003', 'amount' => 50.00, 'statuscode' => 'P']);

        $this->actingAs($this->makeAdmin());

        Livewire::test(StatsOverview::class)
            ->assertSee('Montants ouverts')
            ->assertSee('CHF');
    }

    public function test_stats_widget_shows_active_members(): void
    {
        Member::create([
            'first_name' => 'Active', 'last_name' => 'One',
            'email' => 'dash-a-' . uniqid() . '@test.ch', 'statuscode' => 'A',
        ]);

        $this->actingAs($this->makeAdmin());

        Livewire::test(StatsOverview::class)
            ->assertSee('Membres actives');
    }
}
