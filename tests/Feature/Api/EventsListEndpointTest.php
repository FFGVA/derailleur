<?php

namespace Tests\Feature\Api;

use App\Models\Event;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class EventsListEndpointTest extends TestCase
{
    use DatabaseTransactions;

    public function test_endpoint_returns_published_future_events(): void
    {
        Event::create([
            'title' => 'Sortie publique',
            'starts_at' => now()->addWeek(),
            'statuscode' => 'P',
            'price' => 0,
            'members_only' => false,
        ]);

        $response = $this->getJson('/api/events');

        $response->assertOk();
        $titles = collect($response->json())->pluck('title');
        $this->assertTrue($titles->contains('Sortie publique'));
    }

    public function test_response_includes_members_only_flag(): void
    {
        $restricted = Event::create([
            'title' => 'Sortie membres seulement',
            'starts_at' => now()->addWeek(),
            'statuscode' => 'P',
            'price' => 0,
            'price_non_member' => '9999.99',
            'members_only' => true,
        ]);

        $open = Event::create([
            'title' => 'Sortie ouverte',
            'starts_at' => now()->addWeek(),
            'statuscode' => 'P',
            'price' => 0,
            'members_only' => false,
        ]);

        $response = $this->getJson('/api/events');

        $response->assertOk();
        $byId = collect($response->json())->keyBy('id');
        $this->assertTrue($byId[$restricted->id]['members_only']);
        $this->assertFalse($byId[$open->id]['members_only']);
    }

    public function test_members_only_flag_is_boolean_not_string(): void
    {
        Event::create([
            'title' => 'Sortie membres stricte',
            'starts_at' => now()->addWeek(),
            'statuscode' => 'P',
            'price' => 0,
            'members_only' => true,
        ]);

        $payload = $this->getJson('/api/events')->json();
        $match = collect($payload)->firstWhere('title', 'Sortie membres stricte');

        $this->assertIsBool($match['members_only']);
    }
}
