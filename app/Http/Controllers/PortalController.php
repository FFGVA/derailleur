<?php

namespace App\Http\Controllers;

use App\Enums\EventStatus;
use App\Mail\EventConfirmationMail;
use App\Mail\ExpiredMemberRegistrationMail;
use App\Mail\InvoiceMail;
use App\Mail\MemberUpdateRequestMail;
use App\Models\Event;
use App\Models\EventMember;
use App\Models\Invoice;
use App\Models\Member;
use App\Services\ICalService;
use App\Services\InvoiceService;
use App\Services\PortalAudit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

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
        $member->load('phones');

        return view('portail.adhesion', [
            'member' => $member,
        ]);
    }

    public function adhesionEdit(Request $request)
    {
        $member = $request->attributes->get('portal_member');
        $member->load('phones');

        return view('portail.adhesion-edit', [
            'member' => $member,
        ]);
    }

    public function adhesionUpdate(Request $request)
    {
        $member = $request->attributes->get('portal_member');
        $member->load('phones');

        $request->validate([
            'first_name' => ['required', 'string', 'max:40'],
            'last_name' => ['required', 'string', 'max:60'],
            'email' => ['required', 'email', 'max:255'],
            'address' => ['nullable', 'string'],
            'postal_code' => ['nullable', 'string', 'max:10'],
            'city' => ['nullable', 'string', 'max:255'],
            'phones' => ['nullable', 'array'],
            'phones.*.number' => ['required', 'string', 'max:20'],
            'phones.*.label' => ['nullable', 'string', 'max:40'],
            'phones.*.whatsapp' => ['nullable'],
        ]);

        $changes = $request->only(['first_name', 'last_name', 'email', 'address', 'postal_code', 'city']);
        $changes['phones'] = $request->input('phones', []);

        Mail::send(new MemberUpdateRequestMail($member, $changes));

        PortalAudit::log($request, $member, 'modification', 'Demande de modification envoyée au comité');

        return redirect()->route('portail.adhesion')
            ->with('success', 'Ta demande de modification a été envoyée au comité.');
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

    public function evenement(Request $request, Event $event)
    {
        $member = $request->attributes->get('portal_member');
        $event->load('chefPeloton.phones');

        $pivot = $member->events()
            ->where('events.id', $event->id)
            ->first();

        // Treat cancelled as not registered
        $registration = $pivot?->pivot;
        if ($registration && $registration->status->value === 'X') {
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

        $pivot = EventMember::where('event_id', $event->id)
            ->where('member_id', $member->id)
            ->first();

        // Already actively registered — do nothing
        if ($pivot && $pivot->getRawOriginal('status') !== 'X') {
            return redirect()->route('portail.evenement', $event);
        }

        $applicablePrice = (float) $event->priceForMember($member);
        $newStatus = $applicablePrice > 0 ? 'N' : 'C';

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
            $result = InvoiceService::createEvent($member, $event);
            $invoice = Invoice::where('invoice_number', $result['invoice_number'])->first();
            $invoice->update(['statuscode' => 'E']);

            $qrBase64 = InvoiceService::generateQrCodeBase64($invoice);
            $ical = ICalService::generate($event);
            $icalFilename = ICalService::filename($event);
            Mail::send(new InvoiceMail($invoice, $result['pdf'], $result['filename'], $qrBase64, $ical, $icalFilename));
        } else {
            Mail::send(new EventConfirmationMail($member, $event));
        }

        if ($member->membership_end && $member->membership_end->isPast()) {
            Mail::send(new ExpiredMemberRegistrationMail($member, $event));
        }

        PortalAudit::log($request, $member, 'inscription', "Événement #{$event->id} — {$event->title}");

        return redirect()->route('portail.evenement', $event);
    }

    public function annuler(Request $request, Event $event)
    {
        $member = $request->attributes->get('portal_member');

        $pivot = EventMember::where('event_id', $event->id)
            ->where('member_id', $member->id)
            ->whereIn('status', ['N', 'C'])
            ->whereNull('deleted_at')
            ->first();

        if ($pivot) {
            $pivot->update(['status' => 'X']);
            PortalAudit::log($request, $member, 'annulation', "Événement #{$event->id} — {$event->title}");
        }

        return redirect()->route('portail.evenement', $event);
    }

    public function facturePdf(Request $request, Invoice $invoice)
    {
        $member = $request->attributes->get('portal_member');

        if ($invoice->member_id !== $member->id) {
            abort(403);
        }

        $filename = $invoice->pdf_filename;

        // Try to find existing file
        if ($filename) {
            $path = storage_path('app/private/invoices/' . $filename);
            if (!file_exists($path)) {
                $path = storage_path('app/invoices/' . $filename);
            }
            if (file_exists($path)) {
                return response()->file($path, ['Content-Type' => 'application/pdf']);
            }
        }

        // Generate on the fly if missing
        $result = InvoiceService::generatePdf($invoice);

        return response($result['pdf'], 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $result['filename'] . '"',
        ]);
    }

    public function pelotonMember(Request $request, Event $event, Member $targetMember)
    {
        $member = $request->attributes->get('portal_member');

        if ($event->chef_peloton_id !== $member->id) {
            abort(403);
        }

        $targetMember->load('phones');

        return view('portail.peloton-member', [
            'member' => $member,
            'event' => $event,
            'target' => $targetMember,
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
        $availableMembers = Member::whereIn('statuscode', ['A', 'P', 'N'])
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

            $targetMember = Member::findOrFail($targetMemberId);
            $applicablePrice = (float) $event->priceForMember($targetMember);

            if ($applicablePrice > 0) {
                $result = InvoiceService::createEvent($targetMember, $event);
                $invoice = Invoice::where('invoice_number', $result['invoice_number'])->first();
                $invoice->update(['statuscode' => 'E']);

                $qrBase64 = InvoiceService::generateQrCodeBase64($invoice);
                $ical = ICalService::generate($event);
                $icalFilename = ICalService::filename($event);
                Mail::send(new InvoiceMail($invoice, $result['pdf'], $result['filename'], $qrBase64, $ical, $icalFilename));
            }

            PortalAudit::log($request, $member, 'ajout participante', "Événement #{$event->id} — {$targetMember->first_name} {$targetMember->last_name}");
        }

        return redirect()->route('portail.peloton.event', $event);
    }

    public function uploadGpx(Request $request, Event $event)
    {
        $member = $request->attributes->get('portal_member');

        if ($event->chef_peloton_id !== $member->id) {
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
