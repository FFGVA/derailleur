<?php

namespace App\Http\Controllers;

use App\Enums\EventStatus;
use App\Enums\MemberStatus;
use App\Models\Event;
use App\Models\EventMember;
use App\Models\Member;
use App\Models\MemberMagicToken;
use App\Models\MemberPhone;
use App\Services\EventRegistrationService;
use Illuminate\Http\Request;

class EventRegistrationController extends Controller
{
    public function confirmer(Request $request)
    {
        $rawToken = $request->query('token');
        $eventId = $request->query('event_id');

        if (!$rawToken || !$eventId) {
            return redirect()->route('portail.login')
                ->with('magic_link_error', 'Lien invalide.');
        }

        $magicToken = MemberMagicToken::findByRawToken($rawToken);

        if (!$magicToken || !$magicToken->isValid()) {
            return redirect()->route('portail.login')
                ->with('magic_link_error', 'Ce lien est invalide ou a expiré.');
        }

        $member = Member::where('id', $magicToken->member_id)
            ->whereIn('statuscode', Member::PORTAL_ACCESSIBLE_STATUSES)
            ->first();

        if (!$member) {
            return redirect()->route('portail.login')
                ->with('magic_link_error', 'Ce lien est invalide ou a expiré.');
        }

        $event = Event::where('id', $eventId)
            ->where('statuscode', EventStatus::Publie->value)
            ->whereNull('deleted_at')
            ->first();

        if (!$event) {
            return redirect()->route('portail.login')
                ->with('magic_link_error', 'Cet événement n\'est plus disponible.');
        }

        $magicToken->markUsed();

        // Register for event
        $this->registerForEvent($member, $event);

        // Log into portal
        $request->session()->put('portal_member_id', $member->id);
        $request->session()->put('portal_last_activity', time());

        return redirect()->route('portail.evenement', $event);
    }

    public function nouveauForm(Request $request)
    {
        if (!$request->hasValidSignature()) {
            return redirect()->route('portail.login')
                ->with('magic_link_error', 'Ce lien est invalide ou a expiré.');
        }

        $event = Event::where('id', $request->query('event_id'))
            ->where('statuscode', EventStatus::Publie->value)
            ->whereNull('deleted_at')
            ->first();

        if (!$event) {
            return redirect()->route('portail.login')
                ->with('magic_link_error', 'Cet événement n\'est plus disponible.');
        }

        $email = $request->query('email');
        $price = $event->price_non_member ?? $event->price;

        return view('portail.inscription-event-nouveau', [
            'event' => $event,
            'email' => $email,
            'price' => $price,
        ]);
    }

    public function nouveauStore(Request $request)
    {
        if (!$request->hasValidSignature()) {
            return redirect()->route('portail.login')
                ->with('magic_link_error', 'Ce lien est invalide ou a expiré.');
        }

        $request->validate([
            'prenom' => ['required', 'string', 'max:40'],
            'nom' => ['required', 'string', 'max:60'],
            'email' => ['required', 'email'],
            'telephone' => ['required', 'string', 'max:20'],
            'whatsapp' => ['nullable'],
            'instagram' => ['nullable', 'string', 'max:50'],
            'event_id' => ['required', 'integer'],
        ], [
            'prenom.required' => 'Le prénom est obligatoire.',
            'nom.required' => 'Le nom est obligatoire.',
            'email.required' => 'L\'adresse e-mail est obligatoire.',
            'telephone.required' => 'Le numéro de téléphone est obligatoire.',
        ]);

        $event = Event::where('id', $request->input('event_id'))
            ->where('statuscode', EventStatus::Publie->value)
            ->whereNull('deleted_at')
            ->first();

        if (!$event) {
            return redirect()->route('portail.login')
                ->with('magic_link_error', 'Cet événement n\'est plus disponible.');
        }

        $email = $request->input('email');

        // If member already exists (including soft-deleted), reuse them
        $member = Member::withTrashed()->where('email', $email)->first();

        if ($member && $member->trashed()) {
            $member->restore();
        }

        if (!$member) {
            $metadata = array_filter([
                'instagram' => $request->input('instagram'),
            ]) ?: null;

            $member = Member::create([
                'first_name' => $request->input('prenom'),
                'last_name' => $request->input('nom'),
                'email' => $email,
                'statuscode' => MemberStatus::NonMembre->value,
                'is_invitee' => false,
                'metadata' => $metadata,
            ]);

            MemberPhone::create([
                'member_id' => $member->id,
                'phone_number' => $request->input('telephone'),
                'label' => 'Mobile principal',
                'is_whatsapp' => $request->boolean('whatsapp'),
                'sort_order' => 0,
            ]);
        }

        $this->registerForEvent($member, $event);

        // Log into portal
        $request->session()->put('portal_member_id', $member->id);
        $request->session()->put('portal_last_activity', time());

        return redirect()->route('portail.evenement', $event);
    }

    private function registerForEvent(Member $member, Event $event): void
    {
        EventRegistrationService::register($member, $event);
    }
}
