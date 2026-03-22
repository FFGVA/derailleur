<?php

namespace Tests\Feature;

use App\Models\Event;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ICalFeedTest extends TestCase
{
    use DatabaseTransactions;

    public function test_ical_feed_returns_ics_content_type(): void
    {
        Event::create([
            'title' => 'Sortie Publiée',
            'starts_at' => now()->addWeek(),
            'statuscode' => 'P',
        ]);

        $response = $this->get('/events/ical');

        $response->assertOk();
        $response->assertHeader('content-type', 'text/calendar; charset=UTF-8');
    }

    public function test_ical_feed_includes_published_events(): void
    {
        Event::create([
            'title' => 'Sortie Publiée',
            'starts_at' => now()->addWeek(),
            'statuscode' => 'P',
        ]);

        $response = $this->get('/events/ical');

        $response->assertOk();
        $response->assertSee('Sortie Publi');
    }

    public function test_ical_feed_includes_terminated_events(): void
    {
        Event::create([
            'title' => 'Sortie Terminée',
            'starts_at' => now()->subMonth(),
            'statuscode' => 'T',
        ]);

        $response = $this->get('/events/ical');

        $response->assertOk();
        $response->assertSee('Sortie Termin');
    }

    public function test_ical_feed_excludes_nouveau_events(): void
    {
        Event::create([
            'title' => 'Sortie Brouillon',
            'starts_at' => now()->addWeek(),
            'statuscode' => 'N',
        ]);

        $response = $this->get('/events/ical');

        $response->assertOk();
        $response->assertDontSee('Sortie Brouillon');
    }

    public function test_ical_feed_excludes_cancelled_events(): void
    {
        Event::create([
            'title' => 'Sortie Annulée',
            'starts_at' => now()->addWeek(),
            'statuscode' => 'X',
        ]);

        $response = $this->get('/events/ical');

        $response->assertOk();
        $response->assertDontSee('Sortie Annul');
    }

    public function test_ical_feed_excludes_old_events(): void
    {
        Event::create([
            'title' => 'Sortie Ancienne',
            'starts_at' => now()->subMonths(14),
            'statuscode' => 'T',
        ]);

        $response = $this->get('/events/ical');

        $response->assertOk();
        $response->assertDontSee('Sortie Ancienne');
    }

    public function test_ical_feed_has_valid_vcalendar_structure(): void
    {
        Event::create([
            'title' => 'Test Structure',
            'starts_at' => now()->addWeek(),
            'statuscode' => 'P',
        ]);

        $response = $this->get('/events/ical');

        $content = $response->getContent();
        $this->assertStringContainsString('BEGIN:VCALENDAR', $content);
        $this->assertStringContainsString('END:VCALENDAR', $content);
        $this->assertStringContainsString('BEGIN:VEVENT', $content);
        $this->assertStringContainsString('END:VEVENT', $content);
        $this->assertStringContainsString('PRODID:-//FFGVA//Derailleur//FR', $content);
    }
}
