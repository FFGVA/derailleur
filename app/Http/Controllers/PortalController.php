<?php

namespace App\Http\Controllers;

use App\Enums\EventMemberStatus;
use App\Enums\EventStatus;
use App\Enums\InvoiceStatus;
use App\Mail\EventConfirmationMail;
use App\Mail\ExpiredMemberRegistrationMail;
use App\Mail\MemberUpdateRequestMail;
use App\Mail\AdhesionMail;
use App\Models\Event;
use App\Models\EventMember;
use App\Models\Invoice;
use App\Models\Member;
use App\Models\MemberPhone;
use App\Services\EventRegistrationService;
use App\Services\ICalService;
use App\Services\InvoiceEmailService;
use App\Services\InvoiceService;
use App\Services\MemberCardService;
use App\Services\PortalAudit;
use App\Enums\MemberStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class PortalController extends Controller
{
    public function dashboard(Request $request)
    {
        $member = $request->attributes->get('portal_member');
        $member->load('phones');

        $upcomingEvents = Event::where('starts_at', '>=', now())
            ->where('statuscode', EventStatus::Publie)
            ->whereNull('deleted_at')
            ->orderBy('starts_at')
            ->get();

        // Load member's registration status for each event
        $registrations = $member->events()
            ->whereIn('events.id', $upcomingEvents->pluck('id'))
            ->get()
            ->keyBy('id');

        foreach ($upcomingEvents as $event) {
            $event->memberRegistration = $registrations->get($event->id)?->pivot;
        }

        $isChef = $member->ledEvents()
            ->whereNull('events.deleted_at')
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

    public function protectionDesDonnees()
    {
        return view('portail.protection-des-donnees');
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

    public function adhesionInscription(Request $request)
    {
        $member = $request->attributes->get('portal_member');

        if (!in_array($member->getRawOriginal('statuscode'), [MemberStatus::NonMembre->value, MemberStatus::Brouillon->value])) {
            return redirect()->route('portail.dashboard');
        }

        $member->load('phones');

        return view('portail.adhesion-inscription', [
            'member' => $member,
        ]);
    }

    public function adhesionInscriptionStore(Request $request)
    {
        $member = $request->attributes->get('portal_member');

        if (!in_array($member->getRawOriginal('statuscode'), [MemberStatus::NonMembre->value, MemberStatus::Brouillon->value])) {
            return redirect()->route('portail.dashboard');
        }

        $request->validate([
            'prenom' => ['required', 'string', 'max:40'],
            'nom' => ['required', 'string', 'max:60'],
            'telephone' => ['required', 'string', 'max:20'],
            'photo_ok' => ['required', 'string'],
            'statuts_ok' => ['required'],
            'cotisation_ok' => ['required'],
            'type_velo' => ['nullable', 'string'],
            'sorties' => ['nullable', 'string'],
            'atelier' => ['nullable', 'string'],
            'instagram' => ['nullable', 'string'],
            'strava' => ['nullable', 'string'],
        ], [
            'prenom.required' => 'Le prénom est obligatoire.',
            'nom.required' => 'Le nom est obligatoire.',
            'telephone.required' => 'Le numéro de téléphone est obligatoire.',
            'photo_ok.required' => 'L\'autorisation photos/vidéos est obligatoire.',
            'statuts_ok.required' => 'Tu dois accepter les statuts de l\'association.',
            'cotisation_ok.required' => 'Tu dois accepter la cotisation annuelle.',
        ]);

        $metadata = array_filter([
            'type_velo' => $request->input('type_velo'),
            'sorties' => $request->input('sorties'),
            'atelier' => $request->input('atelier'),
            'instagram' => $request->input('instagram'),
            'strava' => $request->input('strava'),
            'statuts_ok' => $request->input('statuts_ok'),
            'cotisation_ok' => $request->input('cotisation_ok'),
        ]);

        $member->update([
            'first_name' => $request->input('prenom'),
            'last_name' => $request->input('nom'),
            'photo_ok' => $request->input('photo_ok') !== 'non',
            'statuscode' => MemberStatus::EnAttente->value,
            'metadata' => $metadata ?: null,
        ]);

        $phone = $member->phones()->first();
        if ($phone) {
            $phone->update(['phone_number' => $request->input('telephone')]);
        } else {
            MemberPhone::create([
                'member_id' => $member->id,
                'phone_number' => $request->input('telephone'),
                'label' => 'Mobile principal',
            ]);
        }

        // Email already verified (member is logged in)
        $member->update(['email_verified_at' => $member->email_verified_at ?? now()]);

        // Create cotisation invoice + send by email
        $invoice = InvoiceEmailService::createAndSendCotisation($member, (int) date('Y'));

        // Notify admin
        Mail::send(new AdhesionMail(
            nom: $request->input('nom'),
            prenom: $request->input('prenom'),
            email: $member->email,
            telephone: $request->input('telephone'),
            photo_ok: $request->input('photo_ok'),
            type_velo: $request->input('type_velo'),
            sorties: $request->input('sorties'),
            atelier: $request->input('atelier'),
            instagram: $request->input('instagram'),
            strava: $request->input('strava'),
            statuts_ok: $request->input('statuts_ok'),
            cotisation_ok: $request->input('cotisation_ok'),
        ));

        PortalAudit::log($request, $member, 'inscription', 'Adhésion soumise via le portail — facture ' . $invoice->invoice_number);

        return redirect()->route('portail.dashboard');
    }

    public function carte(Request $request)
    {
        $member = $request->attributes->get('portal_member');

        $qrUrl = self::generateCarteToken($member);

        $isActive = in_array($member->getRawOriginal('statuscode'), Member::ACTIVE_STATUSES)
            && (!$member->membership_end || !$member->membership_end->isPast());

        return view('portail.carte', [
            'member' => $member,
            'qrUrl' => $qrUrl,
            'isActive' => $isActive,
        ]);
    }

    public function carteQrUrl(Request $request)
    {
        $member = $request->attributes->get('portal_member');

        return response()->json(['url' => self::generateCarteToken($member)]);
    }

    public function cartePdf(Request $request)
    {
        $member = $request->attributes->get('portal_member');

        if (! in_array($member->getRawOriginal('statuscode'), Member::ACTIVE_STATUSES)) {
            abort(403);
        }

        $pdf = MemberCardService::generate($member);
        $filename = MemberCardService::filename($member);

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Generate a short token for card validation and return the URL.
     * Token is cached for 5 minutes mapping to the member ID.
     */
    private static function generateCarteToken(Member $member): string
    {
        $token = bin2hex(random_bytes(8)); // 16 hex chars
        \Illuminate\Support\Facades\Cache::put("carte_token:{$token}", $member->id, now()->addMinutes(5));

        return url("/carte/v/{$token}");
    }

    public function carteValider(Request $request, string $token)
    {
        $memberId = \Illuminate\Support\Facades\Cache::get("carte_token:{$token}");

        if (!$memberId) {
            return view('portail.carte-valider', [
                'valid' => false,
                'member' => null,
                'reason' => 'Ce lien a expiré. Demande un nouveau QR code.',
            ]);
        }

        $member = Member::find($memberId);

        if (!$member) {
            return view('portail.carte-valider', [
                'valid' => false,
                'member' => null,
                'reason' => 'Membre introuvable.',
            ]);
        }

        $isActive = in_array($member->getRawOriginal('statuscode'), Member::ACTIVE_STATUSES)
            && (!$member->membership_end || !$member->membership_end->isPast());

        return view('portail.carte-valider', [
            'valid' => $isActive,
            'member' => $member,
            'reason' => $isActive ? null : 'Adhésion inactive ou expirée.',
        ]);
    }

    public function factures(Request $request)
    {
        $member = $request->attributes->get('portal_member');
        $invoices = $member->invoices()
            ->whereNull('deleted_at')
            ->whereIn('statuscode', [InvoiceStatus::New->value, InvoiceStatus::Sent->value, InvoiceStatus::Paid->value])
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
