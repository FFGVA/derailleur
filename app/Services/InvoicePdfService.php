<?php

namespace App\Services;

use App\Enums\InvoiceType;
use App\Models\Invoice;
use Fpdf\Fpdf;
use Sprain\SwissQrBill\PaymentPart\Output\FpdfOutput\FpdfOutput;

class InvoicePdfService
{
    /**
     * Generate (or regenerate) PDF for an existing invoice.
     * Returns ['pdf' => string, 'filename' => string, 'invoice_number' => string]
     */
    public static function generate(Invoice $invoice): array
    {
        $invoice->load(['member', 'lines']);
        $member = $invoice->member;
        $amount = $invoice->amount;
        $invoiceNumber = $invoice->invoice_number;
        $date = $invoice->updated_at ? $invoice->updated_at->format('d.m.Y') : now()->format('d.m.Y');

        // Title based on type
        $title = match ($invoice->getRawOriginal('type')) {
            InvoiceType::Cotisation->value => 'Facture — Cotisation annuelle',
            InvoiceType::Evenement->value => 'Facture — Événement',
            default => 'Facture',
        };

        $fpdf = new Fpdf('P', 'mm', 'A4');
        $fpdf->AddPage();
        $fpdf->SetMargins(20, 20, 20);

        // -- Colors from config --
        $brand = config('association.colors.pdf_brand_rgb');
        $textDark = config('association.colors.pdf_text_dark_rgb');
        $textLight = config('association.colors.pdf_text_light_rgb');
        $separator = config('association.colors.pdf_separator_rgb');

        // -- Logo --
        $logoPath = public_path(config('association.logo_path'));
        if (file_exists($logoPath)) {
            $fpdf->Image($logoPath, 20, 15, 40);
        }

        // -- Creditor address --
        $fpdf->SetFont('Helvetica', '', 9);
        $fpdf->SetTextColor(...$textLight);
        $fpdf->SetXY(20, 40);
        $fpdf->Cell(0, 4, self::utf8(config('association.creditor_name')), 0, 1);
        $fpdf->SetX(20);
        $fpdf->Cell(0, 4, self::utf8(config('association.creditor_address')), 0, 1);
        $fpdf->SetX(20);
        $fpdf->Cell(0, 4, self::utf8(config('association.creditor_postal_code') . ' ' . config('association.creditor_city')), 0, 1);

        // -- Brand line --
        $fpdf->SetDrawColor(...$brand);
        $fpdf->SetLineWidth(0.6);
        $fpdf->Line(20, 58, 190, 58);

        // -- Invoice title --
        $fpdf->SetFont('Helvetica', 'B', 18);
        $fpdf->SetTextColor(...$brand);
        $fpdf->SetXY(20, 64);
        $fpdf->Cell(0, 10, self::utf8($title), 0, 1);

        // -- Member details --
        $fpdf->SetFont('Helvetica', '', 11);
        $fpdf->SetTextColor(...$textDark);
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
        $fpdf->SetFillColor(...$brand);
        $fpdf->SetTextColor(255, 255, 255); // white on brand
        $fpdf->SetFont('Helvetica', 'B', 10);
        $fpdf->SetXY(20, $y);
        $fpdf->Cell(130, 8, 'Description', 0, 0, 'L', true);
        $fpdf->Cell(40, 8, 'Montant', 0, 1, 'R', true);

        // Item rows
        $fpdf->SetTextColor(...$textDark);
        $fpdf->SetFont('Helvetica', '', 10);
        foreach ($invoice->lines as $line) {
            $y += 10;
            $fpdf->SetXY(20, $y);
            $fpdf->Cell(130, 8, self::utf8($line->description), 0, 0);
            $fpdf->Cell(40, 8, 'CHF ' . number_format($line->amount, 2, '.', ''), 0, 1, 'R');

            // Line separator
            $fpdf->SetDrawColor(...$separator);
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
        $qrBill = QrBillService::buildQrBill($invoice);

        $output = new FpdfOutput($qrBill, 'fr', $fpdf);
        $output->getPaymentPart();

        $nameSlug = str_replace(' ', '_', $member->last_name . '_' . $member->first_name);
        $nameSlug = preg_replace('/[^a-zA-Z0-9_àâäéèêëïîôùûüçÀÂÄÉÈÊËÏÎÔÙÛÜÇ-]/u', '', $nameSlug);
        $filename = "ffgva_{$nameSlug}-facture-{$invoiceNumber}.pdf";
        $pdfContent = $fpdf->Output('S');

        // Store on disk
        $storagePath = "invoices/{$filename}";
        \Illuminate\Support\Facades\Storage::put($storagePath, $pdfContent);

        // Store filename on invoice record
        $invoice->update(['pdf_filename' => $filename]);

        return [
            'pdf' => $pdfContent,
            'filename' => $filename,
            'invoice_number' => $invoiceNumber,
        ];
    }

    public static function utf8(string $text): string
    {
        return iconv('UTF-8', 'WINDOWS-1252//TRANSLIT', $text) ?: $text;
    }
}
