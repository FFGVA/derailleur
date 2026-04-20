<?php

namespace App\Mail;

use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class AdhesionMail extends BaseMailable
{
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
            from: $this->fromAssociation(),
            to: [$this->contactAddress()],
            replyTo: [$this->email],
            subject: 'Nouvelle demande d\'adhésion FFGVA',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.adhesion',
            with: [
                'nom' => $this->nom,
                'prenom' => $this->prenom,
                'email' => $this->email,
                'telephone' => $this->telephone,
                'photo_ok' => $this->photo_ok,
                'type_velo' => $this->type_velo,
                'sorties' => $this->sorties,
                'atelier' => $this->atelier,
                'instagram' => $this->instagram,
                'strava' => $this->strava,
                'statuts_ok' => $this->statuts_ok,
                'cotisation_ok' => $this->cotisation_ok,
            ],
        );
    }
}
