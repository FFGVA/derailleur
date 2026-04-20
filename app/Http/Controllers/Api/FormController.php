<?php

namespace App\Http\Controllers\Api;

use App\Enums\MemberStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\AdhesionRequest;
use App\Http\Requests\ContactRequest;
use App\Mail\AdhesionMail;
use App\Mail\ContactMail;
use App\Models\Member;
use App\Services\AdhesionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;

class FormController extends Controller
{
    public function contact(ContactRequest $request): JsonResponse
    {
        if ($request->filled('website')) {
            return response()->json(['ok' => true]);
        }

        Mail::send(new ContactMail(
            name: $request->input('name'),
            email: $request->input('email'),
            userMessage: $request->input('message'),
        ));

        return response()->json(['ok' => true]);
    }

    public function adhesion(AdhesionRequest $request): JsonResponse
    {
        if ($request->filled('website')) {
            return response()->json(['ok' => true]);
        }

        $email = $request->input('email');
        $member = Member::where('email', $email)->first();

        $metadata = array_filter([
            'type_velo' => $request->input('type_velo'),
            'sorties' => $request->input('sorties'),
            'atelier' => $request->input('atelier'),
            'instagram' => $request->input('instagram'),
            'strava' => $request->input('strava'),
            'statuts_ok' => $request->input('statuts_ok'),
            'cotisation_ok' => $request->input('cotisation_ok'),
        ]);

        $photoOk = $request->input('photo_ok') !== 'non';

        if ($member && $member->getRawOriginal('statuscode') === MemberStatus::NonMembre->value) {
            AdhesionService::processExistingNonMember($member, $metadata ?: null, $photoOk);
        } else {
            $member = AdhesionService::submitNew([
                'prenom' => $request->input('prenom'),
                'nom' => $request->input('nom'),
                'email' => $email,
                'telephone' => $request->input('telephone'),
                'photo_ok' => $photoOk,
                'metadata' => $metadata ?: null,
            ]);
        }

        Mail::send(new AdhesionMail(
            nom: $request->input('nom'),
            prenom: $request->input('prenom'),
            email: $email,
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

        return response()->json(['ok' => true]);
    }
}
