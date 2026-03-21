<?php

namespace App\Services;

use App\Models\Event;

class ICalService
{
    public static function generate(Event $event): string
    {
        $uid = 'event-' . $event->id . '@ffgva.ch';
        $now = gmdate('Ymd\THis\Z');
        $dtstart = $event->starts_at->utc()->format('Ymd\THis\Z');
        $dtend = $event->ends_at
            ? $event->ends_at->utc()->format('Ymd\THis\Z')
            : $event->starts_at->copy()->addHours(2)->utc()->format('Ymd\THis\Z');

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
            'DTSTART:' . $dtstart,
            'DTEND:' . $dtend,
            'SUMMARY:' . $summary,
            $description ? 'DESCRIPTION:' . $description : null,
            $location ? 'LOCATION:' . $location : null,
            'END:VEVENT',
            'END:VCALENDAR',
        ])) . "\r\n";
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
