<?php

namespace App\Http\Middleware;

use App\Models\Member;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PortalAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $memberId = $request->session()->get('portal_member_id');
        $lastActivity = $request->session()->get('portal_last_activity');

        if (!$memberId || !$lastActivity) {
            return redirect()->route('portail.login');
        }

        $timeout = config('ffgva.portal_session_timeout_minutes', 300);
        $minutesSinceActivity = (now()->timestamp - $lastActivity) / 60;

        if ($minutesSinceActivity > $timeout) {
            $request->session()->forget(['portal_member_id', 'portal_last_activity']);

            return redirect()->route('portail.login')
                ->with('magic_link_error', 'Ta session a expiré. Reconnecte-toi.');
        }

        $member = Member::where('id', $memberId)
            ->whereIn('statuscode', ['A', 'P'])
            ->first();

        if (!$member) {
            $request->session()->forget(['portal_member_id', 'portal_last_activity']);

            return redirect()->route('portail.login');
        }

        $request->attributes->set('portal_member', $member);
        $request->session()->put('portal_last_activity', now()->timestamp);

        return $next($request);
    }
}
