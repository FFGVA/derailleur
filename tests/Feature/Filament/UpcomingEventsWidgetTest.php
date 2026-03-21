<?php

namespace Tests\Feature\Filament;

use App\Filament\Widgets\UpcomingEvents;
use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Tests\TestCase;

class UpcomingEventsWidgetTest extends TestCase
{
    use DatabaseTransactions;

    private function makeAdmin(): User
    {
        return User::create([
            'name' => 'Admin',
            'email' => 'evt-' . uniqid() . '@test.ch',
            'password' => bcrypt('password'),
            'role' => 'A',
        ]);
    }

    public function test_widget_shows_upcoming_event(): void
    {
        Event::create([
            'title' => 'Sortie Vélo Demain',
            'starts_at' => now()->addDay(),
            'ends_at' => now()->addDay()->addHours(3),
            'statuscode' => 'P',
        ]);

        $this->actingAs($this->makeAdmin());

        Livewire::test(UpcomingEvents::class)
            ->assertSee('Sortie Vélo Demain');
    }

    public function test_widget_shows_event_happening_today(): void
    {
        Event::create([
            'title' => 'Sortie Aujourd\'hui',
            'starts_at' => now()->startOfDay(),
            'ends_at' => now()->endOfDay(),
            'statuscode' => 'P',
        ]);

        $this->actingAs($this->makeAdmin());

        Livewire::test(UpcomingEvents::class)
            ->assertSee('Sortie Aujourd\'hui');
    }

    public function test_widget_hides_past_event(): void
    {
        Event::create([
            'title' => 'Sortie Passée',
            'starts_at' => now()->subDays(3),
            'ends_at' => now()->subDays(3)->addHours(3),
            'statuscode' => 'P',
        ]);

        $this->actingAs($this->makeAdmin());

        Livewire::test(UpcomingEvents::class)
            ->assertDontSee('Sortie Passée');
    }

    public function test_widget_shows_event_without_end_date_if_starts_today_or_later(): void
    {
        Event::create([
            'title' => 'Sortie Sans Fin',
            'starts_at' => now()->addDays(2),
            'ends_at' => null,
            'statuscode' => 'P',
        ]);

        $this->actingAs($this->makeAdmin());

        Livewire::test(UpcomingEvents::class)
            ->assertSee('Sortie Sans Fin');
    }

    public function test_widget_hides_cancelled_events(): void
    {
        Event::create([
            'title' => 'Sortie Annulée',
            'starts_at' => now()->addDay(),
            'ends_at' => now()->addDay()->addHours(3),
            'statuscode' => 'X',
        ]);

        $this->actingAs($this->makeAdmin());

        Livewire::test(UpcomingEvents::class)
            ->assertDontSee('Sortie Annulée');
    }
}
