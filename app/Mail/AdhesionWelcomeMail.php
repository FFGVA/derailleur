<?php

namespace App\Mail;

use App\Models\Member;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class AdhesionWelcomeMail extends BaseMailable
{
    public function __construct(
        public Member $member,
        public string $activationUrl,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: $this->fromAssociation(),
            to: [$this->member->email],
            subject: 'Bienvenue chez Fast and Female Geneva !',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.adhesion-welcome',
            with: [
                'member' => $this->member,
                'activationUrl' => $this->activationUrl,
            ],
        );
    }
}
