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

        $newEnd = self::computeMembershipEnd($periodStart);

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

    /**
     * Compute membership end date: 31.12 of the start year,
     * unless start is in Nov/Dec → 31.12 of the following year.
     */
    public static function computeMembershipEnd(\DateTimeInterface $periodStart): \Carbon\Carbon
    {
        $month = (int) $periodStart->format('m');
        $year = (int) $periodStart->format('Y');

        if ($month >= 11) {
            $year++;
        }

        return \Carbon\Carbon::create($year, 12, 31);
    }
}
