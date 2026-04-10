<?php

namespace App\Services;

if (! class_exists('FPDF')) {
    class_alias(\Fpdf\Fpdf::class, 'FPDF');
}

use App\Models\Member;
use setasign\Fpdi\Fpdi;

class MemberCardService
{
    private const TEMPLATE_PATH = 'templates/carte-membre.pdf';

    // Page dimensions: 419.25 x 297.75 pt = 147.9 x 105.0 mm
    private const PAGE_W = 147.9;
    private const PAGE_H = 105.0;

    // Line 1 "Pour qui :" — y: 112.67–126.66pt, x starts at 61.78pt
    private const LINE1_X = 21.8;  // mm
    private const LINE1_Y = 39.7;  // mm (top of text area)
    private const LINE1_H = 5.0;   // mm (height to white-out)

    // Line 2 "Valable jusqu'au : ..." — y: 146.23–160.23pt, x starts at 61.78pt
    private const LINE2_X = 21.8;  // mm
    private const LINE2_Y = 51.6;  // mm
    private const LINE2_H = 5.0;   // mm

    // Cover width (from x start to right margin)
    private const COVER_W = 110.0; // mm

    private const FONT_SIZE = 11;

    public static function generate(Member $member): string
    {
        $templatePath = storage_path('app/' . self::TEMPLATE_PATH);

        $pdf = new Fpdi('L', 'mm', [self::PAGE_W, self::PAGE_H]);
        $pdf->SetAutoPageBreak(false);

        // Page 1: front of card (import as-is)
        $pdf->AddPage('L', [self::PAGE_W, self::PAGE_H]);
        $pdf->setSourceFile($templatePath);
        $tplPage1 = $pdf->importPage(1);
        $pdf->useTemplate($tplPage1, 0, 0, self::PAGE_W, self::PAGE_H);

        // Page 2: back of card
        $pdf->AddPage('L', [self::PAGE_W, self::PAGE_H]);
        $tplPage2 = $pdf->importPage(2);
        $pdf->useTemplate($tplPage2, 0, 0, self::PAGE_W, self::PAGE_H);

        // Background color for white-out (beige #f5f1e9)
        $pdf->SetFillColor(0xf5, 0xf1, 0xe9);
        $pdf->SetDrawColor(0xf5, 0xf1, 0xe9);

        // White-out line 1 ("Pour qui :")
        $pdf->Rect(self::LINE1_X, self::LINE1_Y, self::COVER_W, self::LINE1_H, 'F');

        // White-out line 2 ("Valable jusqu'au : 31 décembre 2026")
        $pdf->Rect(self::LINE2_X, self::LINE2_Y, self::COVER_W, self::LINE2_H, 'F');

        // Write line 1: "Pour qui : " + bold "Prénom Nom"
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetXY(self::LINE1_X, self::LINE1_Y);
        $pdf->SetFont('Helvetica', '', self::FONT_SIZE);
        $prefix1 = 'Pour qui : ';
        $pdf->Cell($pdf->GetStringWidth($prefix1), self::LINE1_H, $prefix1, 0, 0, 'L');
        $pdf->SetFont('Helvetica', 'B', self::FONT_SIZE);
        $name = self::encode($member->first_name . ' ' . $member->last_name);
        $pdf->Cell($pdf->GetStringWidth($name), self::LINE1_H, $name, 0, 0, 'L');

        // Write line 2: "Valable jusqu'au : " + italic "31 décembre {year}"
        $year = (int) date('Y');
        $pdf->SetXY(self::LINE2_X, self::LINE2_Y);
        $pdf->SetFont('Helvetica', '', self::FONT_SIZE);
        $prefix2 = 'Valable jusqu\'au : ';
        $pdf->Cell($pdf->GetStringWidth($prefix2), self::LINE2_H, $prefix2, 0, 0, 'L');
        $pdf->SetFont('Helvetica', 'I', self::FONT_SIZE);
        $date = '31 d' . chr(233) . 'cembre ' . $year;
        $pdf->Cell($pdf->GetStringWidth($date), self::LINE2_H, $date, 0, 0, 'L');

        return $pdf->Output('S');
    }

    public static function filename(Member $member): string
    {
        return 'FFGVA - Membre ' . $member->first_name . ' ' . $member->last_name . '.pdf';
    }

    private static function encode(string $text): string
    {
        return mb_convert_encoding($text, 'ISO-8859-1', 'UTF-8');
    }
}
