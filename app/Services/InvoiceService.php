<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Member;
use Fpdf\Fpdf;
use Sprain\SwissQrBill\DataGroup\Element\CreditorInformation;
use Sprain\SwissQrBill\DataGroup\Element\PaymentAmountInformation;
use Sprain\SwissQrBill\DataGroup\Element\PaymentReference;
use Sprain\SwissQrBill\DataGroup\Element\StructuredAddress;
use Sprain\SwissQrBill\PaymentPart\Output\FpdfOutput\FpdfOutput;
use Sprain\SwissQrBill\QrBill;

class InvoiceService
{
    /**
     * Generate invoice PDF and create DB record.
     * Returns ['pdf' => string, 'filename' => string, 'invoice_number' => string]
     */
    public static function generate(Member $member): array
    {
        $amount = config('ffgva.cotisation_annuelle');
        $invoiceNumber = Invoice::generateNumber($member);
        $date = now()->format('d.m.Y');

        // Create invoice record
        Invoice::create([
            'member_id' => $member->id,
            'invoice_number' => $invoiceNumber,
            'amount' => $amount,
            'statuscode' => 'N',
        ]);

        // Create FPDF instance (A4 portrait, mm units)
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
        $fpdf->Cell(0, 4, 'c/o Livia Wagner', 0, 1);
        $fpdf->SetX(20);
        $fpdf->Cell(0, 4, self::utf8(config('ffgva.creditor_address')), 0, 1);
        $fpdf->SetX(20);
        $fpdf->Cell(0, 4, self::utf8(config('ffgva.creditor_postal_code') . ' ' . config('ffgva.creditor_city')), 0, 1);

        // -- Burgundy line --
        $fpdf->SetDrawColor(128, 8, 28);
        $fpdf->SetLineWidth(0.6);
        $fpdf->Line(20, 62, 190, 62);

        // -- Invoice title --
        $fpdf->SetFont('Helvetica', 'B', 18);
        $fpdf->SetTextColor(128, 8, 28);
        $fpdf->SetXY(20, 70);
        $fpdf->Cell(0, 10, self::utf8('Facture — Cotisation annuelle'), 0, 1);

        // -- Member details --
        $fpdf->SetFont('Helvetica', '', 11);
        $fpdf->SetTextColor(51, 51, 51);
        $y = 88;
        $labelX = 20;
        $valueX = 60;

        $details = [
            'Membre :' => $member->first_name . ' ' . $member->last_name,
            'Date :' => $date,
            self::utf8('Référence :') => $invoiceNumber,
        ];

        if ($member->address || $member->city) {
            $addr = '';
            if ($member->address) {
                $addr .= $member->address;
            }
            if ($member->postal_code || $member->city) {
                if ($addr) $addr .= ', ';
                $addr .= trim(($member->postal_code ?? '') . ' ' . ($member->city ?? ''));
            }
            $details = array_merge(
                ['Membre :' => $member->first_name . ' ' . $member->last_name],
                ['Adresse :' => $addr],
                ['Date :' => $date],
                [self::utf8('Référence :') => $invoiceNumber],
            );
        }

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

        // Item row
        $y += 10;
        $fpdf->SetTextColor(51, 51, 51);
        $fpdf->SetFont('Helvetica', '', 10);
        $fpdf->SetXY(20, $y);
        $fpdf->Cell(130, 8, self::utf8('Cotisation annuelle Fast and Female Geneva ' . date('Y')), 0, 0);
        $fpdf->Cell(40, 8, 'CHF ' . number_format($amount, 2, '.', ''), 0, 1, 'R');

        // Line under item
        $y += 9;
        $fpdf->SetDrawColor(200, 200, 200);
        $fpdf->SetLineWidth(0.3);
        $fpdf->Line(20, $y, 190, $y);

        // Total
        $y += 5;
        $fpdf->SetFont('Helvetica', 'B', 12);
        $fpdf->SetXY(20, $y);
        $fpdf->Cell(170, 8, 'Total : CHF ' . number_format($amount, 2, '.', ''), 0, 1, 'R');

        // -- QR Payment slip (positioned at bottom by the library) --
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
            $amount,
        ));

        // Debtor address is optional — we may not know it yet
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

    private static function utf8(string $text): string
    {
        // FPDF's Helvetica uses Windows-1252 encoding (superset of ISO-8859-1)
        // which supports em dash, curly quotes, etc.
        return iconv('UTF-8', 'WINDOWS-1252//TRANSLIT', $text) ?: $text;
    }
}
