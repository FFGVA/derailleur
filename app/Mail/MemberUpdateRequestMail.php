<?php

namespace App\Mail;

use App\Models\Member;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class MemberUpdateRequestMail extends BaseMailable
{
    public function __construct(
        public Member $member,
        public array $changes,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: $this->fromAssociation(),
            to: [$this->contactAddress()],
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
