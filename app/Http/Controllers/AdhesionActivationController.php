<?php

namespace App\Http\Controllers;

use App\Mail\AdhesionConfirmationMail;
use App\Models\Member;
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AdhesionActivationController extends Controller
{
    public function confirm(Request $request)
    {
        $email = $request->query('email');
        $token = $request->query('token');

        if (! $email || ! $token) {
            return response()->view('adhesion-error', [
                'message' => 'Lien invalide. Veuillez vérifier votre email.',
            ]);
        }

        $member = Member::where('email', $email)->first();

        if (! $member) {
            return response()->view('adhesion-error', [
                'message' => 'Lien invalide. Veuillez vérifier votre email.',
            ]);
        }

        if ($member->email_verified_at !== null) {
            return response()->view('adhesion-error', [
                'message' => 'Votre adresse email a déjà été confirmée.',
            ]);
        }

        if (! $member->activation_token || ! Hash::check($token, $member->activation_token)) {
            return response()->view('adhesion-error', [
                'message' => 'Lien invalide. Veuillez vérifier votre email.',
            ]);
        }

        $expiryHours = config('ffgva.activation_expiry_hours', 72);
        if ($member->activation_sent_at && $member->activation_sent_at->diffInHours(now()) > $expiryHours) {
            return response()->view('adhesion-error', [
                'message' => 'Ce lien a expiré. Veuillez renouveler votre demande.',
            ]);
        }

        $member->update([
            'email_verified_at' => now(),
            'activation_token' => null,
        ]);

        $invoice = InvoiceService::generate($member);

        Mail::send(new AdhesionConfirmationMail($member, $invoice['pdf'], $invoice['filename']));

        return response()->view('adhesion-confirmed', [
            'member' => $member,
        ]);
    }
}
