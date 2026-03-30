<?php

namespace Tests\Unit\Services;

use App\Models\Event;
use App\Services\ICalService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ICalServiceTest extends TestCase
{
    use DatabaseTransactions;

    public function test_generate_returns_valid_ical_with_zurich_timezone(): void
    {
        $event = Event::create([
            'title' => 'Sortie Jura',
            'description' => 'Belle montée',
            'starts_at' => '2026-04-15 09:00:00',
            'ends_at' => '2026-04-15 12:00:00',
            'location' => 'Gex, France',
            'statuscode' => 'P',
            'price' => 0,
        ]);

        $ical = ICalService::generate($event);

        $this->assertStringContainsString('BEGIN:VCALENDAR', $ical);
        $this->assertStringContainsString('BEGIN:VEVENT', $ical);
        $this->assertStringContainsString('SUMMARY:Sortie Jura', $ical);
        $this->assertStringContainsString('LOCATION:Gex\\, France', $ical);
        $this->assertStringContainsString('DESCRIPTION:Belle montée', $ical);
        $this->assertStringContainsString('DTSTART;TZID=Europe/Zurich:20260415T090000', $ical);
        $this->assertStringContainsString('DTEND;TZID=Europe/Zurich:20260415T120000', $ical);
        $this->assertStringNotContainsString('DTSTART:20260415T090000Z', $ical);
        $this->assertStringContainsString('END:VCALENDAR', $ical);
    }

    public function test_generate_without_end_date_defaults_2_hours(): void
    {
        $event = Event::create([
            'title' => 'Sortie rapide',
            'starts_at' => '2026-04-15 09:00:00',
            'statuscode' => 'P',
            'price' => 0,
        ]);

        $ical = ICalService::generate($event);

        $this->assertStringContainsString('DTSTART;TZID=Europe/Zurich:20260415T090000', $ical);
        $this->assertStringContainsString('DTEND;TZID=Europe/Zurich:20260415T110000', $ical);
    }

    public function test_generate_feed_uses_zurich_timezone(): void
    {
        $event = Event::create([
            'title' => 'Sortie feed',
            'starts_at' => '2026-04-15 09:00:00',
            'ends_at' => '2026-04-15 12:00:00',
            'statuscode' => 'P',
            'price' => 0,
        ]);

        $ical = ICalService::generateFeed([$event]);

        $this->assertStringContainsString('DTSTART;TZID=Europe/Zurich:20260415T090000', $ical);
        $this->assertStringContainsString('DTEND;TZID=Europe/Zurich:20260415T120000', $ical);
        $this->assertStringNotContainsString('T090000Z', $ical);
    }

    public function test_filename_sanitizes_title(): void
    {
        $event = Event::create([
            'title' => 'Sortie à vélo — Col de la Faucille!',
            'starts_at' => '2026-04-15 09:00:00',
            'statuscode' => 'P',
            'price' => 0,
        ]);

        $filename = ICalService::filename($event);

        $this->assertStringStartsWith('ffgva-', $filename);
        $this->assertStringEndsWith('.ics', $filename);
        $this->assertMatchesRegularExpression('/^ffgva-[a-z0-9-]+\.ics$/', $filename);
    }
}
