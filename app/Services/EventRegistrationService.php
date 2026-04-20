<?php

namespace App\Services;

use App\Enums\EventMemberStatus;
use App\Mail\EventConfirmationMail;
use App\Models\Event;
use App\Models\EventMember;
use App\Models\Member;
use Illuminate\Support\Facades\Mail;

class EventRegistrationService
{
    /**
     * Register a member for an event. Creates/updates pivot, sends invoice or confirmation.
     * Returns false if already actively registered (no-op).
     */
    public static function register(Member $member, Event $event): bool
    {
        $applicablePrice = (float) $event->priceForMember($member);

        $pivot = EventMember::where('event_id', $event->id)
            ->where('member_id', $member->id)
            ->first();

        if ($pivot && $pivot->getRawOriginal('status') !== EventMemberStatus::Annule->value) {
            return false;
        }

        $newStatus = $applicablePrice > 0
            ? EventMemberStatus::Inscrit->value
            : EventMemberStatus::Confirme->value;

        if ($pivot) {
            $pivot->update(['status' => $newStatus]);
        } else {
            EventMember::create([
                'event_id' => $event->id,
                'member_id' => $member->id,
                'status' => $newStatus,
            ]);
        }

        if ($applicablePrice > 0) {
            InvoiceEmailService::createAndSendEvent($member, $event);
        } else {
            Mail::send(new EventConfirmationMail($member, $event));
        }

        return true;
    }
}
