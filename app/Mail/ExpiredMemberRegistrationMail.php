<?php

namespace App\Mail;

use App\Models\Event;
use App\Models\Member;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class ExpiredMemberRegistrationMail extends BaseMailable
{
    public function __construct(
        public Member $member,
        public Event $event,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: $this->fromAssociation(),
            to: [$this->contactAddress()],
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
