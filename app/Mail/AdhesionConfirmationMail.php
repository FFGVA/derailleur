<?php

namespace App\Mail;

use App\Models\Member;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class AdhesionConfirmationMail extends BaseMailable
{
    public function __construct(
        public Member $member,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: $this->fromAssociation(),
            to: [$this->member->email],
            replyTo: [$this->replyToAssociation()],
            subject: 'Confirmation de ton inscription - FFGVA',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.adhesion-confirmation',
            with: [
                'member' => $this->member,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
