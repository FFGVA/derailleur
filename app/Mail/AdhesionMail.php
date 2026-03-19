<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdhesionMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $nom,
        public string $prenom,
        public string $email,
        public string $telephone,
        public string $photo_ok,
        public ?string $type_velo = null,
        public ?string $sorties = null,
        public ?string $atelier = null,
        public ?string $instagram = null,
        public ?string $strava = null,
        public ?string $statuts_ok = null,
        public ?string $cotisation_ok = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            to: [config('mail.to_contact')],
            replyTo: [$this->email],
            subject: 'Nouvelle demande d\'adhésion FFGVA',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.adhesion',
        );
    }
}
