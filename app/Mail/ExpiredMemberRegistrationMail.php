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

class ExpiredMemberRegistrationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Member $member,
        public Event $event,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('noreply@ffgva.ch', 'Fast and Female Geneva - Ne pas répondre'),
            to: [config('ffgva.contact_email')],
            subject: 'Inscription membre expirée — ' . $this->member->first_name . ' ' . $this->member->last_name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.expired-member-registration',
        );
    }
}
