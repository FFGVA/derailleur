<?php

namespace App\Mail;

use App\Models\Event;
use App\Models\Member;
use App\Services\ICalService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EventConfirmationMail extends Mailable
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
            to: [$this->member->email],
            replyTo: [new Address('fastandfemalegva@etik.com', 'Fast and Female Geneva')],
            subject: 'Inscription confirmée — ' . $this->event->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.event-confirmation',
        );
    }

    public function attachments(): array
    {
        $ical = ICalService::generate($this->event);
        $filename = ICalService::filename($this->event);

        return [
            Attachment::fromData(fn () => $ical, $filename)
                ->withMime('text/calendar'),
        ];
    }
}
