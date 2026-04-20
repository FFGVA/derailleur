<?php

namespace App\Services;

use App\Enums\InvoiceType;
use App\Enums\MemberStatus;
use App\Mail\ActivationMail;
use App\Models\Invoice;
use App\Models\Member;
use Illuminate\Support\Facades\Mail;

class InvoicePaymentService
{
    /**
     * Handle post-payment processing for a cotisation invoice.
     * Extends membership, activates member, sends activation email.
     */
    public static function onCotisationPaid(Invoice $invoice): void
    {
        if ($invoice->getRawOriginal('type') !== InvoiceType::Cotisation->value) {
            return;
        }

        $member = $invoice->member;

        if ($member->membership_end) {
            $periodStart = $member->membership_end->copy()->addDay();
        } else {
            $periodStart = now();
        }

        $newEnd = InvoiceService::computeMembershipEnd($periodStart);

        $wasActive = in_array($member->getRawOriginal('statuscode'), Member::ACTIVE_STATUSES);

        $member->update([
            'membership_end' => $newEnd,
            'statuscode' => MemberStatus::Actif->value,
            'membership_requested_at' => null,
        ]);

        if (! $wasActive) {
            Mail::send(new ActivationMail($member));
        }
    }
}
