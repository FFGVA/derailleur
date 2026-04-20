<?php

namespace App\Services;

use App\Models\Invoice;
use Sprain\SwissQrBill\DataGroup\Element\AdditionalInformation;
use Sprain\SwissQrBill\DataGroup\Element\CreditorInformation;
use Sprain\SwissQrBill\DataGroup\Element\PaymentAmountInformation;
use Sprain\SwissQrBill\DataGroup\Element\PaymentReference;
use Sprain\SwissQrBill\DataGroup\Element\StructuredAddress;
use Sprain\SwissQrBill\QrBill;

class QrBillService
{
    /**
     * Build a QrBill instance for an invoice.
     */
    public static function buildQrBill(Invoice $invoice): QrBill
    {
        $invoice->loadMissing('member');
        $member = $invoice->member;

        $qrBill = QrBill::create();

        $qrBill->setCreditor(StructuredAddress::createWithStreet(
            config('association.creditor_name'),
            config('association.creditor_address'),
            null,
            config('association.creditor_postal_code'),
            config('association.creditor_city'),
            config('association.creditor_country'),
        ));

        $qrBill->setCreditorInformation(CreditorInformation::create(config('association.iban')));

        $qrBill->setPaymentAmountInformation(PaymentAmountInformation::create(
            config('association.currency'),
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
}
