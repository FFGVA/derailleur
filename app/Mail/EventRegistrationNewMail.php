<?php

namespace App\Mail;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EventRegistrationNewMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $email,
        public Event $event,
        public string $registrationUrl,
        public string $price,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('noreply@ffgva.ch', 'Fast and Female Geneva - Ne pas répondre'),
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
