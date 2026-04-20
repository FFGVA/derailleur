<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdhesionRequest;
use App\Http\Requests\ContactRequest;
use App\Mail\AdhesionConfirmationMail;
use App\Mail\AdhesionMail;
use App\Mail\AdhesionWelcomeMail;
use App\Mail\ContactMail;
use App\Mail\InvoiceMail;
use App\Models\Member;
use App\Models\MemberPhone;
use App\Services\InvoiceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
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

        if (! $member) {
            $member = Member::create([
                'first_name' => $request->input('prenom'),
                'last_name' => $request->input('nom'),
                'email' => $email,
                'is_invitee' => false,
                'photo_ok' => $photoOk,
                'statuscode' => 'P',
                'metadata' => $metadata ?: null,
            ]);

            MemberPhone::create([
                'member_id' => $member->id,
                'phone_number' => $request->input('telephone'),
                'label' => 'Mobile principal',
            ]);
        } elseif ($member->getRawOriginal('statuscode') === 'P') {
            $member->update([
                'first_name' => $request->input('prenom'),
                'last_name' => $request->input('nom'),
                'photo_ok' => $photoOk,
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
        }

        // Non-member with verified email: skip activation, send invoice directly
        if ($member->getRawOriginal('statuscode') === 'N') {
            $member->update([
                'membership_requested_at' => now(),
                'metadata' => $metadata ?: $member->metadata,
                'photo_ok' => $photoOk,
            ]);

            $result = InvoiceService::generate($member);
            $invoice = \App\Models\Invoice::where('invoice_number', $result['invoice_number'])->first();
            $qrImage = InvoiceService::generateQrCodeBase64($invoice);
            Mail::send(new InvoiceMail(
                invoice: $invoice,
                pdfContent: $result['pdf'],
                pdfFilename: $result['filename'],
                qrImageBase64: $qrImage,
            ));
            $invoice->update(['statuscode' => 'E']);

            Mail::send(new AdhesionConfirmationMail($member));
        } else {
            // New member or re-submission: send activation email
            $rawToken = bin2hex(random_bytes(32));
            $member->update([
                'activation_token' => Hash::make($rawToken),
                'activation_sent_at' => now(),
            ]);

            $activationUrl = url("/adhesion/confirmer?token={$rawToken}&email={$member->email}");
            Mail::send(new AdhesionWelcomeMail($member, $activationUrl));
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
