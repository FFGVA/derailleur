<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Enums\EventMemberStatus;
use App\Mail\ExpiredMemberRegistrationMail;
use App\Models\Event;
use App\Models\EventMember;
use App\Services\EventRegistrationService;
use App\Services\ICalService;
use App\Services\PortalAudit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EventController extends Controller
{
    public function evenement(Request $request, Event $event)
    {
        $member = $request->attributes->get('portal_member');
        $event->load('chefs.phones');

        $pivot = $member->events()
            ->where('events.id', $event->id)
            ->first();

        // Treat cancelled as not registered
        $registration = $pivot?->pivot;
        if ($registration && $registration->status->value === EventMemberStatus::Annule->value) {
            $registration = null;
        }

        return view('portail.evenement', [
            'member' => $member,
            'event' => $event,
            'registration' => $registration,
            'applicablePrice' => (float) $event->priceForMember($member),
        ]);
    }

    public function evenementIcal(Request $request, Event $event)
    {
        $ical = ICalService::generate($event);
        $filename = ICalService::filename($event);

        return response($ical, 200, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function inscrire(Request $request, Event $event)
    {
        $member = $request->attributes->get('portal_member');

        $registered = EventRegistrationService::register($member, $event);

        if ($registered && $member->membership_end && $member->membership_end->isPast()) {
            Mail::send(new ExpiredMemberRegistrationMail($member, $event));
        }

        if ($registered) {
            PortalAudit::log($request, $member, 'inscription', "Événement #{$event->id} — {$event->title}");
        }

        return redirect()->route('portail.evenement', $event);
    }

    public function annuler(Request $request, Event $event)
    {
        $member = $request->attributes->get('portal_member');

        $pivot = EventMember::where('event_id', $event->id)
            ->where('member_id', $member->id)
            ->whereIn('status', [EventMemberStatus::Inscrit->value, EventMemberStatus::Confirme->value])
            ->whereNull('deleted_at')
            ->first();

        if ($pivot) {
            $pivot->update(['status' => EventMemberStatus::Annule->value]);
            PortalAudit::log($request, $member, 'annulation', "Événement #{$event->id} — {$event->title}");
        }

        return redirect()->route('portail.evenement', $event);
    }
}
