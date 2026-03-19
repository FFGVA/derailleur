<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdhesionRequest;
use App\Http\Requests\ContactRequest;
use App\Mail\AdhesionMail;
use App\Mail\ContactMail;
use App\Models\Member;
use App\Models\MemberPhone;
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

        if (! $member) {
            $metadata = array_filter([
                'type_velo' => $request->input('type_velo'),
                'sorties' => $request->input('sorties'),
                'atelier' => $request->input('atelier'),
                'instagram' => $request->input('instagram'),
                'strava' => $request->input('strava'),
                'statuts_ok' => $request->input('statuts_ok'),
                'cotisation_ok' => $request->input('cotisation_ok'),
                'photo_ok' => $request->input('photo_ok'),
            ]);

            $member = Member::create([
                'first_name' => $request->input('prenom'),
                'last_name' => $request->input('nom'),
                'email' => $email,
                'is_invitee' => false,
                'statuscode' => 'P',
                'metadata' => $metadata ?: null,
            ]);

            MemberPhone::create([
                'member_id' => $member->id,
                'phone_number' => $request->input('telephone'),
                'label' => 'mobile',
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
