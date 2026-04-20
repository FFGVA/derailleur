<?php

namespace App\Mail;

use App\Models\Invoice;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class InvoiceMail extends BaseMailable
{
    public function __construct(
        public Invoice $invoice,
        public string $pdfContent,
        public string $pdfFilename,
        public ?string $qrImageBase64 = null,
        public ?string $icalContent = null,
        public ?string $icalFilename = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: $this->fromAssociation(),
            to: [$this->invoice->member->email],
            replyTo: [$this->replyToAssociation()],
            subject: 'Facture ' . $this->invoice->invoice_number . ' - FFGVA',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.invoice',
            with: [
                'invoice' => $this->invoice,
                'member' => $this->invoice->member,
                'lines' => $this->invoice->lines,
                'qrImageBase64' => $this->qrImageBase64,
            ],
        );
    }

    public function attachments(): array
    {
        $attachments = [
            Attachment::fromData(fn () => $this->pdfContent, $this->pdfFilename)
                ->withMime('application/pdf'),
        ];

        if ($this->icalContent && $this->icalFilename) {
            $attachments[] = Attachment::fromData(fn () => $this->icalContent, $this->icalFilename)
                ->withMime('text/calendar');
        }

        return $attachments;
    }
}
