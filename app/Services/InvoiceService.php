<?php

namespace App\Services;

use App\Enums\InvoiceStatus;
use App\Enums\InvoiceType;
use App\Models\Event;
use App\Models\Invoice;
use App\Models\InvoiceLine;
use App\Models\Member;

/**
 * Invoice creation service.
 *
 * PDF generation → InvoicePdfService
 * QR code generation → QrBillService
 * Payment processing → InvoicePaymentService
 * Creation + email → InvoiceEmailService
 */
class InvoiceService
{
    /**
     * Create a cotisation invoice with line and generate PDF.
     */
    public static function createCotisation(Member $member, int $year, ?float $amount = null): Invoice
    {
        $amount = $amount ?? config('association.cotisation_annuelle');

        $invoice = Invoice::create([
            'member_id' => $member->id,
            'type' => InvoiceType::Cotisation->value,
            'cotisation_year' => $year,
            'invoice_number' => Invoice::generateNumber($member),
            'amount' => $amount,
            'statuscode' => InvoiceStatus::New->value,
        ]);

        if ($member->membership_end) {
            $periodStart = $member->membership_end->copy()->addDay();
        } else {
            $periodStart = now();
        }
        $periodEnd = InvoicePaymentService::computeMembershipEnd($periodStart);

        InvoiceLine::create([
            'invoice_id' => $invoice->id,
            'description' => "Cotisation annuelle {$year} — période du {$periodStart->format('d.m.Y')} au {$periodEnd->format('d.m.Y')}",
            'amount' => $amount,
            'sort_order' => 0,
        ]);

        InvoicePdfService::generate($invoice);

        return $invoice;
    }

    /**
     * Create an event invoice with lines and generate PDF.
     */
    public static function createEvent(Member $member, Event|array $events): Invoice
    {
        $events = is_array($events) ? $events : [$events];
        $totalAmount = collect($events)->sum(fn ($e) => (float) $e->priceForMember($member));

        $invoice = Invoice::create([
            'member_id' => $member->id,
            'type' => InvoiceType::Evenement->value,
            'invoice_number' => Invoice::generateNumber($member),
            'amount' => $totalAmount,
            'statuscode' => InvoiceStatus::New->value,
        ]);

        foreach ($events as $i => $event) {
            $invoice->events()->attach($event->id);

            InvoiceLine::create([
                'invoice_id' => $invoice->id,
                'description' => $event->title . ' — ' . $event->starts_at->format('d.m.Y'),
                'amount' => $event->priceForMember($member),
                'sort_order' => $i,
            ]);
        }

        InvoicePdfService::generate($invoice);

        return $invoice;
    }

    /**
     * Create an "autre" invoice — lines must be added separately, then call InvoicePdfService::generate().
     */
    public static function createAutre(Member $member, ?string $notes = null): Invoice
    {
        return Invoice::create([
            'member_id' => $member->id,
            'type' => InvoiceType::Autre->value,
            'invoice_number' => Invoice::generateNumber($member),
            'amount' => 0,
            'statuscode' => InvoiceStatus::New->value,
            'notes' => $notes,
        ]);
    }

    /**
     * Legacy method for adhesion flow — creates a cotisation invoice.
     */
    public static function generate(Member $member): Invoice
    {
        return self::createCotisation($member, (int) date('Y'));
    }

    // ── Delegates (kept for backward compatibility) ──

    /** @deprecated Use InvoicePdfService::generate() */
    public static function generatePdf(Invoice $invoice): array
    {
        return InvoicePdfService::generate($invoice);
    }

    /** @deprecated Use QrBillService::generateQrCodeBase64() */
    public static function generateQrCodeBase64(Invoice $invoice): ?string
    {
        return QrBillService::generateQrCodeBase64($invoice);
    }

    /** @deprecated Use InvoicePaymentService::onCotisationPaid() */
    public static function onCotisationPaid(Invoice $invoice): void
    {
        InvoicePaymentService::onCotisationPaid($invoice);
    }

    /** @deprecated Use InvoicePaymentService::computeMembershipEnd() */
    public static function computeMembershipEnd(\DateTimeInterface $periodStart): \Carbon\Carbon
    {
        return InvoicePaymentService::computeMembershipEnd($periodStart);
    }

    public static function utf8(string $text): string
    {
        return InvoicePdfService::utf8($text);
    }
}
