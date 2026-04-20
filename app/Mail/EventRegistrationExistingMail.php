<?php

namespace App\Mail;

use App\Models\Event;
use App\Models\Member;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class EventRegistrationExistingMail extends BaseMailable
{
    public function __construct(
        public Member $member,
        public Event $event,
        public string $confirmUrl,
        public string $expiresAt,
        public string $applicablePrice,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: $this->fromAssociation(),
            to: [$this->member->email],
            subject: 'Inscription — ' . $this->event->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.event-registration-existing',
        );
    }
}
