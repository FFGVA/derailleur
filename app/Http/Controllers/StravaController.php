<?php

namespace App\Http\Controllers;

use App\Models\MemberStrava;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class StravaController extends Controller
{
    /**
     * Redirect to Strava OAuth authorization page.
     */
    public function redirect(Request $request)
    {
        $user = $request->user();
        abort_unless($user?->isAdmin(), 403);

        $clientId = config('association.strava_client_id');
        abort_unless($clientId, 500, 'STRAVA_CLIENT_ID non configuré dans .env');

        $params = http_build_query([
            'client_id' => $clientId,
            'redirect_uri' => route('strava.callback'),
            'response_type' => 'code',
            'approval_prompt' => 'auto',
            'scope' => 'read,profile:read_all',
        ]);

        return redirect("https://www.strava.com/oauth/authorize?{$params}");
    }

    /**
     * Handle the OAuth callback from Strava.
     */
    public function callback(Request $request)
    {
        $user = $request->user();
        abort_unless($user?->isAdmin(), 403);

        if ($request->has('error')) {
            return redirect('/admin/strava/connect')->with('strava_error', 'Autorisation refusée par Strava.');
        }

        $code = $request->query('code');
        abort_unless($code, 400, 'Code d\'autorisation manquant');

        $response = Http::post('https://www.strava.com/oauth/token', [
            'client_id' => config('association.strava_client_id'),
            'client_secret' => config('association.strava_client_secret'),
            'code' => $code,
            'grant_type' => 'authorization_code',
        ]);

        if (!$response->successful()) {
            Log::error('Strava token exchange failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return redirect('/admin/strava/connect')->with('strava_error', 'Erreur lors de l\'échange du token Strava (HTTP ' . $response->status() . ').');
        }

        $data = $response->json();
        $athleteId = $data['athlete']['id'];
        $athleteName = trim(($data['athlete']['firstname'] ?? '') . ' ' . ($data['athlete']['lastname'] ?? ''));

        $stravaLink = MemberStrava::updateOrCreate(
            ['strava_athlete_id' => $athleteId],
            [
                'access_token' => $data['access_token'],
                'refresh_token' => $data['refresh_token'],
                'token_expires_at' => \Carbon\Carbon::createFromTimestamp($data['expires_at']),
                'scopes' => $data['scope'] ?? 'read,profile:read_all',
                'modified_by_id' => $user->id,
            ]
        );

        $user->update(['strava_id' => $stravaLink->id]);

        return redirect('/admin/strava/connect')->with('strava_success', "Compte Strava connecté : {$athleteName} (athlète #{$athleteId})");
    }

    /**
     * Disconnect a Strava account (soft-delete the link).
     */
    public function disconnect(Request $request)
    {
        $user = $request->user();
        abort_unless($user?->isAdmin(), 403);

        $stravaId = $request->input('strava_id');
        $link = MemberStrava::find($stravaId);

        if ($link) {
            try {
                Http::post('https://www.strava.com/oauth/deauthorize', [
                    'access_token' => $link->access_token,
                ]);
            } catch (\Exception $e) {
                Log::warning('Strava deauthorize failed', ['error' => $e->getMessage()]);
            }

            \App\Models\User::where('strava_id', $link->id)->update(['strava_id' => null]);
            $link->delete();
        }

        return redirect('/admin/strava/connect')->with('strava_success', 'Compte Strava déconnecté.');
    }

    /**
     * Refresh an expired access token.
     */
    public static function refreshToken(MemberStrava $link): bool
    {
        if ($link->token_expires_at->isFuture()) {
            return true;
        }

        $response = Http::post('https://www.strava.com/oauth/token', [
            'client_id' => config('association.strava_client_id'),
            'client_secret' => config('association.strava_client_secret'),
            'grant_type' => 'refresh_token',
            'refresh_token' => $link->refresh_token,
        ]);

        if (!$response->successful()) {
            Log::error('Strava token refresh failed', [
                'strava_id' => $link->id,
                'status' => $response->status(),
            ]);
            return false;
        }

        $data = $response->json();
        $link->update([
            'access_token' => $data['access_token'],
            'refresh_token' => $data['refresh_token'],
            'token_expires_at' => \Carbon\Carbon::createFromTimestamp($data['expires_at']),
        ]);

        return true;
    }
}
