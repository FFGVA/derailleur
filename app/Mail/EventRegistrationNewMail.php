<?php

namespace App\Mail;

use App\Models\Event;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class EventRegistrationNewMail extends BaseMailable
{
    public function __construct(
        public string $email,
        public Event $event,
        public string $registrationUrl,
        public string $price,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: $this->fromAssociation(),
            to: [$this->email],
            subject: 'Inscription — ' . $this->event->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.event-registration-new',
        );
    }
}
