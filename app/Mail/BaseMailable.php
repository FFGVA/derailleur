<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;

abstract class BaseMailable extends Mailable
{
    use Queueable, SerializesModels;

    protected function fromAssociation(): Address
    {
        return new Address(
            config('association.mail_from_address'),
            config('association.mail_from_name'),
        );
    }

    protected function replyToAssociation(): Address
    {
        return new Address(
            config('association.mail_reply_to_address'),
            config('association.mail_reply_to_name'),
        );
    }

    protected function contactAddress(): string
    {
        return config('association.contact_email');
    }
}
