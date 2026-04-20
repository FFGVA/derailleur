<?php

namespace App\Mail;

use App\Models\Member;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class PortalMagicLinkMail extends BaseMailable
{
    public function __construct(
        public Member $member,
        public string $magicLinkUrl,
        public string $expiresAt,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: $this->fromAssociation(),
            to: [$this->member->email],
            subject: 'Ton lien de connexion',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.portal-magic-link',
        );
    }
}
