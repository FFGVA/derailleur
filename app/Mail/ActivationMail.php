<?php

namespace App\Mail;

use App\Models\Member;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ActivationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Member $member,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('noreply@ffgva.ch', 'Fast and Female Geneva - Ne pas répondre'),
            to: [$this->member->email],
            replyTo: [new Address('fastandfemalegva@etik.com', 'Fast and Female Geneva')],
            subject: 'Bienvenue chez Fast and Female Geneva !',
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
