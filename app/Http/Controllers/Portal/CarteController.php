<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Services\MemberCardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CarteController extends Controller
{
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
        Cache::put("carte_token:{$token}", $member->id, now()->addMinutes(5));

        return url("/carte/v/{$token}");
    }

    public function carteValider(Request $request, string $token)
    {
        $memberId = Cache::get("carte_token:{$token}");

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
}
