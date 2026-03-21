<?php

namespace App\Http\Controllers;

use App\Enums\EventStatus;
use App\Mail\InvoiceMail;
use App\Models\Event;
use App\Models\EventMember;
use App\Models\Invoice;
use App\Models\Member;
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PortalController extends Controller
{
    public function dashboard(Request $request)
    {
        $member = $request->attributes->get('portal_member');
        $member->load('phones');

        $upcomingEvents = $member->events()
            ->where('starts_at', '>=', now())
            ->where('events.statuscode', EventStatus::Publie)
            ->whereNull('events.deleted_at')
            ->orderBy('starts_at')
            ->get();

        $isChef = $member->ledEvents()
            ->whereNull('deleted_at')
            ->where('starts_at', '>=', now()->subWeek())
            ->exists();

        return view('portail.dashboard', [
            'member' => $member,
            'upcomingEvents' => $upcomingEvents,
            'isChef' => $isChef,
        ]);
    }

    public function adhesion(Request $request)
    {
        $member = $request->attributes->get('portal_member');

        return view('portail.adhesion', [
            'member' => $member,
        ]);
    }

    public function factures(Request $request)
    {
        $member = $request->attributes->get('portal_member');
        $invoices = $member->invoices()
            ->whereNull('deleted_at')
            ->whereIn('statuscode', ['N', 'E', 'P'])
            ->orderByDesc('updated_at')
            ->with('lines')
            ->get();

        return view('portail.factures', [
            'member' => $member,
            'invoices' => $invoices,
        ]);
    }

    public function facturePdf(Request $request, Invoice $invoice)
    {
        $member = $request->attributes->get('portal_member');

        if ($invoice->member_id !== $member->id) {
            abort(403);
        }

        $nameSlug = str_replace(' ', '_', $member->last_name . '_' . $member->first_name);
        $nameSlug = preg_replace('/[^a-zA-Z0-9_àâäéèêëïîôùûüçÀÂÄÉÈÊËÏÎÔÙÛÜÇ-]/u', '', $nameSlug);
        $filename = "ffgva_{$nameSlug}-facture-{$invoice->invoice_number}.pdf";
        $path = storage_path('app/invoices/' . $filename);

        if (!file_exists($path)) {
            abort(404);
        }

        return response()->file($path, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    public function peloton(Request $request)
    {
        $member = $request->attributes->get('portal_member');

        $events = $member->ledEvents()
            ->whereNull('deleted_at')
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

        if ($event->chef_peloton_id !== $member->id) {
            abort(403);
        }

        $participants = $event->members()
            ->whereNull('event_member.deleted_at')
            ->with('phones')
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        $participantIds = $participants->pluck('id');
        $availableMembers = Member::whereIn('statuscode', ['A', 'P'])
            ->whereNull('deleted_at')
            ->whereNotIn('id', $participantIds)
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get(['id', 'first_name', 'last_name']);

        return view('portail.peloton-event', [
            'member' => $member,
            'event' => $event,
            'participants' => $participants,
            'availableMembers' => $availableMembers,
        ]);
    }

    public function togglePresence(Request $request, Event $event, Member $targetMember)
    {
        $member = $request->attributes->get('portal_member');

        if ($event->chef_peloton_id !== $member->id) {
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

        return redirect()->route('portail.peloton.event', $event);
    }

    public function addParticipant(Request $request, Event $event)
    {
        $member = $request->attributes->get('portal_member');

        if ($event->chef_peloton_id !== $member->id) {
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
                'status' => 'C',
                'present' => true,
            ]);

            if ($event->price > 0) {
                $targetMember = Member::findOrFail($targetMemberId);
                $result = InvoiceService::createEvent($targetMember, $event);
                $invoice = Invoice::where('invoice_number', $result['invoice_number'])->first();
                $invoice->update(['statuscode' => 'E']);

                $qrBase64 = InvoiceService::generateQrCodeBase64($invoice);
                Mail::send(new InvoiceMail($invoice, $result['pdf'], $result['filename'], $qrBase64));
            }
        }

        return redirect()->route('portail.peloton.event', $event);
    }
}
