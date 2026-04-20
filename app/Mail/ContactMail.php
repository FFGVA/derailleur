<?php

namespace App\Mail;

use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class ContactMail extends BaseMailable
{
    public function __construct(
        public string $name,
        public string $email,
        public string $userMessage,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: $this->fromAssociation(),
            to: [$this->contactAddress()],
            replyTo: [$this->email],
            subject: 'Nouveau message via le site FFGVA',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact',
        );
    }
}
