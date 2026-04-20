<?php

namespace App\Services;

use App\Enums\InvoiceStatus;
use App\Mail\InvoiceMail;
use App\Models\Event;
use App\Models\Invoice;
use App\Models\Member;
use Illuminate\Support\Facades\Mail;

class InvoiceEmailService
{
    /**
     * Create a cotisation invoice, generate PDF, send email, mark as sent.
     */
    public static function createAndSendCotisation(Member $member, int $year, ?float $amount = null): Invoice
    {
        $invoice = InvoiceService::createCotisation($member, $year, $amount);

        self::sendExisting($invoice);

        return $invoice;
    }

    /**
     * Create an event invoice, generate PDF, send email with iCal, mark as sent.
     */
    public static function createAndSendEvent(Member $member, Event|array $events): Invoice
    {
        $invoice = InvoiceService::createEvent($member, $events);

        $singleEvent = is_array($events) ? $events[0] : $events;
        $ical = ICalService::generate($singleEvent);
        $icalFilename = ICalService::filename($singleEvent);

        $pdfResult = InvoiceService::generatePdf($invoice);
        $qrBase64 = InvoiceService::generateQrCodeBase64($invoice);

        Mail::send(new InvoiceMail(
            $invoice, $pdfResult['pdf'], $pdfResult['filename'], $qrBase64, $ical, $icalFilename
        ));

        $invoice->update(['statuscode' => InvoiceStatus::Sent->value]);

        return $invoice;
    }

    /**
     * Send an existing invoice by email (regenerate PDF) and mark as sent.
     */
    public static function sendExisting(Invoice $invoice): void
    {
        $pdfResult = InvoiceService::generatePdf($invoice);
        $qrBase64 = InvoiceService::generateQrCodeBase64($invoice);

        Mail::send(new InvoiceMail(
            $invoice, $pdfResult['pdf'], $pdfResult['filename'], $qrBase64
        ));

        $invoice->update(['statuscode' => InvoiceStatus::Sent->value]);
    }
}
