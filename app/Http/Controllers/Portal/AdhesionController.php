<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Enums\MemberStatus;
use App\Mail\AdhesionMail;
use App\Mail\MemberUpdateRequestMail;
use App\Models\MemberPhone;
use App\Services\InvoiceEmailService;
use App\Services\PortalAudit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AdhesionController extends Controller
{
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

        $member->setPhone($request->input('telephone'));

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
}
