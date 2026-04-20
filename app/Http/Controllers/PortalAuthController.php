<?php

namespace App\Http\Controllers;

use App\Enums\MemberStatus;
use App\Http\Requests\PortalMagicLinkRequest;
use App\Mail\PortalMagicLinkMail;
use App\Models\Member;
use App\Models\MemberMagicToken;
use App\Services\AdhesionService;
use App\Services\PortalAudit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PortalAuthController extends Controller
{
    public function login(Request $request)
    {
        if ($request->session()->has('portal_member_id')) {
            return redirect()->route('portail.dashboard');
        }

        return view('portail.login');
    }

    public function sendLink(PortalMagicLinkRequest $request)
    {
        $email = $request->validated()['email'];

        $member = Member::where('email', $email)
            ->whereIn('statuscode', Member::PORTAL_ACCESSIBLE_STATUSES)
            ->first();

        if ($member) {
            [$token, $rawToken] = MemberMagicToken::generateFor($member);

            $magicLinkUrl = url('/auth/verify/' . $rawToken);

            Mail::to($member->email)->send(new PortalMagicLinkMail(
                member: $member,
                magicLinkUrl: $magicLinkUrl,
                expiresAt: $token->expires_at->format('d.m.Y à H:i'),
            ));
        }

        return redirect()->route('portail.login')
            ->with('magic_link_success', true)
            ->with('magic_link_email', $email);
    }

    public function verifyToken(string $token)
    {
        $magicToken = MemberMagicToken::findByRawToken($token);

        if (!$magicToken || !$magicToken->isValid()) {
            return redirect()->route('portail.login')
                ->with('magic_link_error', 'Ce lien est invalide ou a expiré. Demande un nouveau lien.');
        }

        $member = Member::where('id', $magicToken->member_id)
            ->whereIn('statuscode', Member::PORTAL_ACCESSIBLE_STATUSES)
            ->first();

        if (!$member) {
            return redirect()->route('portail.login')
                ->with('magic_link_error', 'Ce lien est invalide ou a expiré. Demande un nouveau lien.');
        }

        $magicToken->markUsed();

        // If member is P (email unconfirmed from adhesion), confirm now — magic link proves email works
        if ($member->getRawOriginal('statuscode') === MemberStatus::EnAttente->value) {
            AdhesionService::confirmEmail($member);
        }

        session([
            'portal_member_id' => $member->id,
            'portal_last_activity' => now()->timestamp,
        ]);

        request()->session()->regenerate();

        PortalAudit::log(request(), $member, 'login');

        return redirect()->route('portail.dashboard');
    }

    public function registerForm()
    {
        return view('portail.register');
    }

    public function registerStore(Request $request)
    {
        // Honeypot
        if ($request->filled('website')) {
            return view('portail.register-confirmation');
        }

        $request->validate([
            'prenom' => ['required', 'string', 'max:40'],
            'nom' => ['required', 'string', 'max:60'],
            'email' => ['required', 'email', 'max:255'],
            'telephone' => ['required', 'string', 'max:20'],
        ], [
            'prenom.required' => 'Le prénom est obligatoire.',
            'nom.required' => 'Le nom est obligatoire.',
            'email.required' => 'L\'adresse email est obligatoire.',
            'email.email' => 'L\'adresse email n\'est pas valide.',
            'telephone.required' => 'Le numéro de téléphone est obligatoire.',
        ]);

        $email = $request->input('email');
        $member = Member::where('email', $email)->first();

        if (! $member) {
            $member = Member::create([
                'first_name' => $request->input('prenom'),
                'last_name' => $request->input('nom'),
                'email' => $email,
                'statuscode' => MemberStatus::NonMembre->value,
                'is_invitee' => false,
            ]);

            $member->setPhone($request->input('telephone'));
        }

        // Send magic link (regardless of new or existing — don't reveal)
        if (in_array($member->getRawOriginal('statuscode'), Member::PORTAL_ACCESSIBLE_STATUSES)) {
            [$token, $rawToken] = MemberMagicToken::generateFor($member);
            $magicLinkUrl = url('/auth/verify/' . $rawToken);

            Mail::send(new PortalMagicLinkMail(
                member: $member,
                magicLinkUrl: $magicLinkUrl,
                expiresAt: $token->expires_at->format('d.m.Y à H:i'),
            ));
        }

        return view('portail.register-confirmation');
    }

    public function logout(Request $request)
    {
        $memberId = $request->session()->get('portal_member_id');
        if ($memberId) {
            $member = Member::find($memberId);
            if ($member) {
                PortalAudit::log($request, $member, 'logout');
            }
        }

        $request->session()->forget(['portal_member_id', 'portal_last_activity']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('portail.login');
    }
}
