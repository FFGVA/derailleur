<?php

namespace App\Services;

use App\Models\Event;

class ICalService
{
    private const TIMEZONE = 'Europe/Zurich';

    public static function generate(Event $event): string
    {
        $now = gmdate('Ymd\THis\Z');
        $vevent = self::formatEvent($event, $now);

        return implode("\r\n", array_filter([
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//FFGVA//Derailleur//FR',
            'CALSCALE:GREGORIAN',
            'METHOD:PUBLISH',
            ...$vevent,
            'END:VCALENDAR',
        ])) . "\r\n";
    }

    public static function generateFeed(iterable $events): string
    {
        $lines = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//FFGVA//Derailleur//FR',
            'CALSCALE:GREGORIAN',
            'METHOD:PUBLISH',
            'X-WR-CALNAME:' . config('association.name'),
        ];

        $now = gmdate('Ymd\THis\Z');

        foreach ($events as $event) {
            array_push($lines, ...self::formatEvent($event, $now));
        }

        $lines[] = 'END:VCALENDAR';

        return implode("\r\n", $lines) . "\r\n";
    }

    /**
     * Format a single event as VEVENT lines.
     */
    private static function formatEvent(Event $event, string $dtstamp): array
    {
        $dtstart = $event->starts_at->format('Ymd\THis');
        $dtend = $event->ends_at
            ? $event->ends_at->format('Ymd\THis')
            : $event->starts_at->copy()->addHours(2)->format('Ymd\THis');

        $summary = self::escape($event->title);
        $description = self::escape($event->description ?? '');
        $location = self::escape($event->location ?? '');

        return array_filter([
            'BEGIN:VEVENT',
            'UID:event-' . $event->id . '@ffgva.ch',
            'DTSTAMP:' . $dtstamp,
            'DTSTART;TZID=' . self::TIMEZONE . ':' . $dtstart,
            'DTEND;TZID=' . self::TIMEZONE . ':' . $dtend,
            'SUMMARY:' . $summary,
            $description ? 'DESCRIPTION:' . $description : null,
            $location ? 'LOCATION:' . $location : null,
            'END:VEVENT',
        ]);
    }

    public static function filename(Event $event): string
    {
        $slug = strtolower($event->title);
        $slug = transliterator_transliterate('Any-Latin; Latin-ASCII; Lower()', $slug);
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
        $slug = trim($slug, '-');

        return 'ffgva-' . $slug . '.ics';
    }

    private static function escape(string $text): string
    {
        $text = str_replace(['\\', ';', ',', "\n"], ['\\\\', '\\;', '\\,', '\\n'], $text);

        return $text;
    }
}
