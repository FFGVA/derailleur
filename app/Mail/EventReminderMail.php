<?php

namespace App\Mail;

use App\Models\Event;
use App\Models\Member;
use App\Services\ICalService;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class EventReminderMail extends BaseMailable
{
    public function __construct(
        public Member $member,
        public Event $event,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: $this->fromAssociation(),
            to: [$this->member->email],
            replyTo: [$this->replyToAssociation()],
            subject: 'Rappel — ' . $this->event->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.event-reminder',
        );
    }

    public function attachments(): array
    {
        $ical = ICalService::generate($this->event);
        $filename = ICalService::filename($this->event);

        return [
            Attachment::fromData(fn () => $ical, $filename)
                ->withMime('text/calendar'),
        ];
    }
}
