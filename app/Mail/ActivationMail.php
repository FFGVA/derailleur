<?php

namespace App\Mail;

use App\Models\Member;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class ActivationMail extends BaseMailable
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
            subject: 'Bienvenue chez ' . config('association.name') . ' !',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.activation',
        );
    }

    public function attachments(): array
    {
        $voucherPath = storage_path('app/templates/voucher-ffgva.pdf');

        return [
            Attachment::fromPath($voucherPath)
                ->as('VOUCHER FFGVA.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
