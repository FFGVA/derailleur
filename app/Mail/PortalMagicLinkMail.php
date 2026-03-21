<?php

namespace App\Mail;

use App\Models\Member;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PortalMagicLinkMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Member $member,
        public string $magicLinkUrl,
        public string $expiresAt,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('noreply@ffgva.ch', 'Fast and Female Geneva - Ne pas répondre'),
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
