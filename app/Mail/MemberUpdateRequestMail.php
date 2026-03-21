<?php

namespace App\Mail;

use App\Models\Member;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MemberUpdateRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Member $member,
        public array $changes,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('noreply@ffgva.ch', 'Fast and Female Geneva - Ne pas répondre'),
            to: [config('ffgva.contact_email')],
            replyTo: [$this->member->email],
            subject: 'Demande de modification — ' . $this->member->first_name . ' ' . $this->member->last_name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.member-update-request',
        );
    }
}
