<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Invoice;
use App\Models\InvoiceLine;
use App\Models\Member;
use Fpdf\Fpdf;
use Sprain\SwissQrBill\DataGroup\Element\AdditionalInformation;
use Sprain\SwissQrBill\DataGroup\Element\CreditorInformation;
use Sprain\SwissQrBill\DataGroup\Element\PaymentAmountInformation;
use Sprain\SwissQrBill\DataGroup\Element\PaymentReference;
use Sprain\SwissQrBill\DataGroup\Element\StructuredAddress;
use Sprain\SwissQrBill\PaymentPart\Output\FpdfOutput\FpdfOutput;
use Sprain\SwissQrBill\QrBill;

class InvoiceService
{
    /**
     * Create a cotisation invoice with line and generate PDF.
     */
    public static function createCotisation(Member $member, int $year, ?float $amount = null): array
    {
        $amount = $amount ?? config('ffgva.cotisation_annuelle');

        $invoice = Invoice::create([
            'member_id' => $member->id,
            'type' => 'C',
            'cotisation_year' => $year,
            'invoice_number' => Invoice::generateNumber($member),
            'amount' => $amount,
            'statuscode' => 'N',
        ]);

        // Next period: starts day after current membership_end, lasts 1 year
        if ($member->membership_end) {
            $periodStart = $member->membership_end->copy()->addDay();
            $periodEnd = $periodStart->copy()->addYear()->subDay();
        } else {
            $periodStart = now();
            $periodEnd = now()->addYear();
        }

        InvoiceLine::create([
            'invoice_id' => $invoice->id,
            'description' => "Cotisation annuelle {$year} — période du {$periodStart->format('d.m.Y')} au {$periodEnd->format('d.m.Y')}",
            'amount' => $amount,
            'sort_order' => 0,
        ]);

        return self::generatePdf($invoice);
    }

    /**
     * Create an event invoice with lines and generate PDF.
     * Accepts one or more events.
     */
    public static function createEvent(Member $member, Event|array $events): array
    {
        $events = is_array($events) ? $events : [$events];
        $totalAmount = collect($events)->sum('price');

        $invoice = Invoice::create([
            'member_id' => $member->id,
            'type' => 'E',
            'invoice_number' => Invoice::generateNumber($member),
            'amount' => $totalAmount,
            'statuscode' => 'N',
        ]);

        // Attach events via pivot
        foreach ($events as $i => $event) {
            $invoice->events()->attach($event->id);

            InvoiceLine::create([
                'invoice_id' => $invoice->id,
                'description' => $event->title . ' — ' . $event->starts_at->format('d.m.Y'),
                'amount' => $event->price,
                'sort_order' => $i,
            ]);
        }

        return self::generatePdf($invoice);
    }

    /**
     * Create an "autre" invoice — lines must be added separately, then call generatePdf.
     */
    public static function createAutre(Member $member, ?string $notes = null): Invoice
    {
        return Invoice::create([
            'member_id' => $member->id,
            'type' => 'A',
            'invoice_number' => Invoice::generateNumber($member),
            'amount' => 0,
            'statuscode' => 'N',
            'notes' => $notes,
        ]);
    }

    /**
     * Generate (or regenerate) PDF for an existing invoice.
     * Returns ['pdf' => string, 'filename' => string, 'invoice_number' => string]
     */
    public static function generatePdf(Invoice $invoice): array
    {
        $invoice->load(['member', 'lines']);
        $member = $invoice->member;
        $amount = $invoice->amount;
        $invoiceNumber = $invoice->invoice_number;
        $date = $invoice->updated_at ? $invoice->updated_at->format('d.m.Y') : now()->format('d.m.Y');

        // Title based on type
        $title = match ($invoice->getRawOriginal('type')) {
            'C' => 'Facture — Cotisation annuelle',
            'E' => 'Facture — Événement',
            default => 'Facture',
        };

        $fpdf = new Fpdf('P', 'mm', 'A4');
        $fpdf->AddPage();
        $fpdf->SetMargins(20, 20, 20);

        // -- Logo --
        $logoPath = public_path('images/logo-ffgva.png');
        if (file_exists($logoPath)) {
            $fpdf->Image($logoPath, 20, 15, 40);
        }

        // -- Creditor address --
        $fpdf->SetFont('Helvetica', '', 9);
        $fpdf->SetTextColor(102, 102, 102);
        $fpdf->SetXY(20, 40);
        $fpdf->Cell(0, 4, self::utf8(config('ffgva.creditor_name')), 0, 1);
        $fpdf->SetX(20);
        $fpdf->Cell(0, 4, self::utf8(config('ffgva.creditor_address')), 0, 1);
        $fpdf->SetX(20);
        $fpdf->Cell(0, 4, self::utf8(config('ffgva.creditor_postal_code') . ' ' . config('ffgva.creditor_city')), 0, 1);

        // -- Burgundy line --
        $fpdf->SetDrawColor(128, 8, 28);
        $fpdf->SetLineWidth(0.6);
        $fpdf->Line(20, 58, 190, 58);

        // -- Invoice title --
        $fpdf->SetFont('Helvetica', 'B', 18);
        $fpdf->SetTextColor(128, 8, 28);
        $fpdf->SetXY(20, 64);
        $fpdf->Cell(0, 10, self::utf8($title), 0, 1);

        // -- Member details --
        $fpdf->SetFont('Helvetica', '', 11);
        $fpdf->SetTextColor(51, 51, 51);
        $y = 82;
        $labelX = 20;
        $valueX = 60;

        $details = ['Membre :' => $member->first_name . ' ' . $member->last_name];

        if ($member->address || $member->city) {
            $addr = '';
            if ($member->address) $addr .= $member->address;
            if ($member->postal_code || $member->city) {
                if ($addr) $addr .= ', ';
                $addr .= trim(($member->postal_code ?? '') . ' ' . ($member->city ?? ''));
            }
            $details['Adresse :'] = $addr;
        }

        $details['Date :'] = $date;
        $details[self::utf8('Référence :')] = $invoiceNumber;

        foreach ($details as $label => $value) {
            $fpdf->SetFont('Helvetica', 'B', 10);
            $fpdf->SetXY($labelX, $y);
            $fpdf->Cell(38, 6, $label, 0, 0);
            $fpdf->SetFont('Helvetica', '', 10);
            $fpdf->SetXY($valueX, $y);
            $fpdf->Cell(0, 6, self::utf8($value), 0, 1);
            $y += 7;
        }

        // -- Items table --
        $y += 8;

        // Header row
        $fpdf->SetFillColor(128, 8, 28);
        $fpdf->SetTextColor(255, 255, 255);
        $fpdf->SetFont('Helvetica', 'B', 10);
        $fpdf->SetXY(20, $y);
        $fpdf->Cell(130, 8, 'Description', 0, 0, 'L', true);
        $fpdf->Cell(40, 8, 'Montant', 0, 1, 'R', true);

        // Item rows
        $fpdf->SetTextColor(51, 51, 51);
        $fpdf->SetFont('Helvetica', '', 10);
        foreach ($invoice->lines as $line) {
            $y += 10;
            $fpdf->SetXY(20, $y);
            $fpdf->Cell(130, 8, self::utf8($line->description), 0, 0);
            $fpdf->Cell(40, 8, 'CHF ' . number_format($line->amount, 2, '.', ''), 0, 1, 'R');

            // Line separator
            $fpdf->SetDrawColor(200, 200, 200);
            $fpdf->SetLineWidth(0.3);
            $fpdf->Line(20, $y + 9, 190, $y + 9);
        }

        // If no lines yet, show a placeholder
        if ($invoice->lines->isEmpty()) {
            $y += 10;
            $fpdf->SetXY(20, $y);
            $fpdf->Cell(130, 8, self::utf8('(aucune ligne)'), 0, 0);
            $fpdf->Cell(40, 8, 'CHF 0.00', 0, 1, 'R');
        }

        // Total
        $y += 14;
        $fpdf->SetFont('Helvetica', 'B', 12);
        $fpdf->SetXY(20, $y);
        $fpdf->Cell(170, 8, 'Total : CHF ' . number_format($amount, 2, '.', ''), 0, 1, 'R');

        // -- QR Payment slip --
        $qrBill = self::buildQrBill($invoice);

        $output = new FpdfOutput($qrBill, 'fr', $fpdf);
        $output->getPaymentPart();

        $nameSlug = str_replace(' ', '_', $member->last_name . '_' . $member->first_name);
        $nameSlug = preg_replace('/[^a-zA-Z0-9_àâäéèêëïîôùûüçÀÂÄÉÈÊËÏÎÔÙÛÜÇ-]/u', '', $nameSlug);
        $filename = "ffgva_{$nameSlug}-facture-{$invoiceNumber}.pdf";
        $pdfContent = $fpdf->Output('S');

        // Store on disk
        $storagePath = "invoices/{$filename}";
        \Illuminate\Support\Facades\Storage::put($storagePath, $pdfContent);

        return [
            'pdf' => $pdfContent,
            'filename' => $filename,
            'invoice_number' => $invoiceNumber,
        ];
    }

    /**
     * Legacy method for adhesion flow — creates a cotisation invoice.
     */
    public static function generate(Member $member): array
    {
        return self::createCotisation($member, (int) date('Y'));
    }

    /**
     * Generate a QR code image as base64 data URI for embedding in emails.
     */
    public static function generateQrCodeBase64(Invoice $invoice): ?string
    {
        try {
            $qrBill = self::buildQrBill($invoice);
            $qrCode = $qrBill->getQrCode();

            return $qrCode->getDataUri('png');
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Build a QrBill instance for an invoice. Used by both PDF and email QR code generation.
     */
    private static function buildQrBill(Invoice $invoice): QrBill
    {
        $invoice->loadMissing('member');
        $member = $invoice->member;

        $qrBill = QrBill::create();

        $qrBill->setCreditor(StructuredAddress::createWithStreet(
            config('ffgva.creditor_name'),
            config('ffgva.creditor_address'),
            null,
            config('ffgva.creditor_postal_code'),
            config('ffgva.creditor_city'),
            config('ffgva.creditor_country'),
        ));

        $qrBill->setCreditorInformation(CreditorInformation::create(config('ffgva.iban')));

        $qrBill->setPaymentAmountInformation(PaymentAmountInformation::create(
            'CHF',
            (float) $invoice->amount,
        ));

        if ($member->postal_code && $member->city) {
            $qrBill->setUltimateDebtor(StructuredAddress::createWithoutStreet(
                $member->first_name . ' ' . $member->last_name,
                $member->postal_code,
                $member->city,
                $member->country ?? 'CH',
            ));
        }

        $qrBill->setPaymentReference(PaymentReference::create(
            PaymentReference::TYPE_NON,
        ));

        $qrBill->setAdditionalInformation(AdditionalInformation::create(
            'Facture ' . $invoice->invoice_number,
        ));

        return $qrBill;
    }

    /**
     * Update member's membership_end when a cotisation invoice is paid.
     * New end = current membership_end + 1 year (or 1 year from today if no end date).
     */
    public static function onCotisationPaid(Invoice $invoice): void
    {
        if ($invoice->getRawOriginal('type') !== 'C') {
            return;
        }

        $member = $invoice->member;

        if ($member->membership_end) {
            $newEnd = $member->membership_end->copy()->addDay()->addYear()->subDay();
        } else {
            $newEnd = now()->addYear();
        }

        $member->update(['membership_end' => $newEnd]);
    }

    public static function utf8(string $text): string
    {
        return iconv('UTF-8', 'WINDOWS-1252//TRANSLIT', $text) ?: $text;
    }
}
