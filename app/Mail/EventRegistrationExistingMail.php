<?php

namespace App\Mail;

use App\Models\Event;
use App\Models\Member;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EventRegistrationExistingMail extends Mailable
{
    use Queueable, SerializesModels;

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
            from: new Address('noreply@ffgva.ch', 'Fast and Female Geneva - Ne pas répondre'),
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
