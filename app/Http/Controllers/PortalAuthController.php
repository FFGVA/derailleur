<?php

namespace App\Http\Controllers;

use App\Http\Requests\PortalMagicLinkRequest;
use App\Mail\PortalMagicLinkMail;
use App\Models\Member;
use App\Models\MemberMagicToken;
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
            ->whereIn('statuscode', ['A', 'P'])
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
            ->whereIn('statuscode', ['A', 'P'])
            ->first();

        if (!$member) {
            return redirect()->route('portail.login')
                ->with('magic_link_error', 'Ce lien est invalide ou a expiré. Demande un nouveau lien.');
        }

        $magicToken->markUsed();

        session([
            'portal_member_id' => $member->id,
            'portal_last_activity' => now()->timestamp,
        ]);

        request()->session()->regenerate();

        PortalAudit::log(request(), $member, 'login');

        return redirect()->route('portail.dashboard');
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
