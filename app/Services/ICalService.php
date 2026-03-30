<?php

namespace App\Services;

use App\Models\Event;

class ICalService
{
    private const TIMEZONE = 'Europe/Zurich';

    public static function generate(Event $event): string
    {
        $uid = 'event-' . $event->id . '@ffgva.ch';
        $now = gmdate('Ymd\THis\Z');
        $dtstart = $event->starts_at->format('Ymd\THis');
        $dtend = $event->ends_at
            ? $event->ends_at->format('Ymd\THis')
            : $event->starts_at->copy()->addHours(2)->format('Ymd\THis');

        $summary = self::escape($event->title);
        $description = self::escape($event->description ?? '');
        $location = self::escape($event->location ?? '');

        return implode("\r\n", array_filter([
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//FFGVA//Derailleur//FR',
            'CALSCALE:GREGORIAN',
            'METHOD:PUBLISH',
            'BEGIN:VEVENT',
            'UID:' . $uid,
            'DTSTAMP:' . $now,
            'DTSTART;TZID=' . self::TIMEZONE . ':' . $dtstart,
            'DTEND;TZID=' . self::TIMEZONE . ':' . $dtend,
            'SUMMARY:' . $summary,
            $description ? 'DESCRIPTION:' . $description : null,
            $location ? 'LOCATION:' . $location : null,
            'END:VEVENT',
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
            'X-WR-CALNAME:Fast and Female Geneva',
        ];

        $now = gmdate('Ymd\THis\Z');

        foreach ($events as $event) {
            $dtstart = $event->starts_at->format('Ymd\THis');
            $dtend = $event->ends_at
                ? $event->ends_at->format('Ymd\THis')
                : $event->starts_at->copy()->addHours(2)->format('Ymd\THis');

            $summary = self::escape($event->title);
            $description = self::escape($event->description ?? '');
            $location = self::escape($event->location ?? '');

            $lines[] = 'BEGIN:VEVENT';
            $lines[] = 'UID:event-' . $event->id . '@ffgva.ch';
            $lines[] = 'DTSTAMP:' . $now;
            $lines[] = 'DTSTART;TZID=' . self::TIMEZONE . ':' . $dtstart;
            $lines[] = 'DTEND;TZID=' . self::TIMEZONE . ':' . $dtend;
            $lines[] = 'SUMMARY:' . $summary;
            if ($description) {
                $lines[] = 'DESCRIPTION:' . $description;
            }
            if ($location) {
                $lines[] = 'LOCATION:' . $location;
            }
            $lines[] = 'END:VEVENT';
        }

        $lines[] = 'END:VCALENDAR';

        return implode("\r\n", $lines) . "\r\n";
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
