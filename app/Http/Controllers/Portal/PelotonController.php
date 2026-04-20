<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Enums\EventMemberStatus;
use App\Enums\InvoiceStatus;
use App\Enums\MemberStatus;
use App\Models\Event;
use App\Models\EventMember;
use App\Models\Member;
use App\Services\InvoiceEmailService;
use App\Services\PortalAudit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PelotonController extends Controller
{
    public function peloton(Request $request)
    {
        $member = $request->attributes->get('portal_member');

        $events = $member->ledEvents()
            ->whereNull('events.deleted_at')
            ->where('starts_at', '>=', now()->subWeek())
            ->orderBy('starts_at')
            ->get();

        return view('portail.peloton', [
            'member' => $member,
            'events' => $events,
        ]);
    }

    public function pelotonEvent(Request $request, Event $event)
    {
        $member = $request->attributes->get('portal_member');

        if (!$event->chefs->contains('id', $member->id)) {
            abort(403);
        }

        $participants = $event->members()
            ->whereNull('event_member.deleted_at')
            ->with('phones')
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        $participantIds = $participants->pluck('id');
        $availableMembers = Member::whereIn('statuscode', [MemberStatus::Actif->value, MemberStatus::EnAttente->value, MemberStatus::NonMembre->value])
            ->whereNull('deleted_at')
            ->whereNotIn('id', $participantIds)
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get(['id', 'first_name', 'last_name']);

        // Find members with open invoices for this event
        $openInvoiceMemberIds = \App\Models\Invoice::whereIn('statuscode', [InvoiceStatus::New->value, InvoiceStatus::Sent->value])
            ->whereNull('deleted_at')
            ->whereHas('events', fn ($q) => $q->where('events.id', $event->id))
            ->pluck('member_id')
            ->unique()
            ->toArray();

        return view('portail.peloton-event', [
            'member' => $member,
            'event' => $event,
            'participants' => $participants,
            'availableMembers' => $availableMembers,
            'openInvoiceMemberIds' => $openInvoiceMemberIds,
        ]);
    }

    public function pelotonMember(Request $request, Event $event, Member $targetMember)
    {
        $member = $request->attributes->get('portal_member');

        if (!$event->chefs->contains('id', $member->id)) {
            abort(403);
        }

        // Ensure target is either a participant of this event or the chef herself
        $isParticipant = $event->members()
            ->whereNull('event_member.deleted_at')
            ->where('members.id', $targetMember->id)
            ->exists();

        if (!$isParticipant && $targetMember->id !== $member->id) {
            abort(403);
        }

        $targetMember->load('phones');

        return view('portail.peloton-member', [
            'member' => $member,
            'event' => $event,
            'target' => $targetMember,
        ]);
    }

    public function togglePresence(Request $request, Event $event, Member $targetMember)
    {
        $member = $request->attributes->get('portal_member');

        if (!$event->chefs->contains('id', $member->id)) {
            abort(403);
        }

        $pivot = EventMember::where('event_id', $event->id)
            ->where('member_id', $targetMember->id)
            ->whereNull('deleted_at')
            ->firstOrFail();

        // 3-state toggle: null → true → false → null
        $pivot->present = match ($pivot->getRawOriginal('present')) {
            null => true,
            1, true => false,
            0, false => null,
        };
        $pivot->save();

        $presenceLabel = match ($pivot->getRawOriginal('present')) {
            1, true => 'présente',
            0, false => 'absente',
            default => 'non défini',
        };
        PortalAudit::log($request, $member, 'présence', "Événement #{$event->id} — {$targetMember->first_name} {$targetMember->last_name}: {$presenceLabel}");

        return redirect()->route('portail.peloton.event', $event);
    }

    public function addParticipant(Request $request, Event $event)
    {
        $member = $request->attributes->get('portal_member');

        if (!$event->chefs->contains('id', $member->id)) {
            abort(403);
        }

        $request->validate([
            'member_id' => ['required', 'exists:members,id'],
        ]);

        $targetMemberId = $request->input('member_id');

        $exists = EventMember::where('event_id', $event->id)
            ->where('member_id', $targetMemberId)
            ->exists();

        if (!$exists) {
            EventMember::create([
                'event_id' => $event->id,
                'member_id' => $targetMemberId,
                'status' => EventMemberStatus::Confirme->value,
                'present' => true,
            ]);

            $targetMember = Member::findOrFail($targetMemberId);
            $applicablePrice = (float) $event->priceForMember($targetMember);

            if ($applicablePrice > 0) {
                InvoiceEmailService::createAndSendEvent($targetMember, $event);
            }

            PortalAudit::log($request, $member, 'ajout participante', "Événement #{$event->id} — {$targetMember->first_name} {$targetMember->last_name}");
        }

        return redirect()->route('portail.peloton.event', $event);
    }

    public function uploadGpx(Request $request, Event $event)
    {
        $member = $request->attributes->get('portal_member');

        if (!$event->chefs->contains('id', $member->id)) {
            abort(403);
        }

        $request->validate([
            'gpx_file' => ['required', 'file', 'max:5120'],
        ]);

        $file = $request->file('gpx_file');

        // Delete old file if exists
        if ($event->gpx_file && Storage::disk('public')->exists($event->gpx_file)) {
            Storage::disk('public')->delete($event->gpx_file);
        }

        $path = $file->store('gpx', 'public');
        $event->update(['gpx_file' => $path]);

        PortalAudit::log($request, $member, 'upload gpx', "Événement #{$event->id} — {$event->title}");

        return redirect()->route('portail.peloton.event', $event);
    }
}
