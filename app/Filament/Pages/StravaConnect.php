<?php

namespace App\Filament\Pages;

use App\Http\Controllers\StravaController;
use App\Models\MemberStrava;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Http;

class StravaConnect extends Page
{
    protected static ?string $title = 'Strava — Connexion';

    protected static ?string $slug = 'strava/connect';

    protected static string $view = 'filament.pages.strava-connect';

    protected static bool $shouldRegisterNavigation = false;

    public function mount(): void
    {
        if (session('strava_success')) {
            Notification::make()
                ->title(session('strava_success'))
                ->success()
                ->send();
        }

        if (session('strava_error')) {
            Notification::make()
                ->title(session('strava_error'))
                ->danger()
                ->send();
        }
    }

    public function getConnection(): ?MemberStrava
    {
        return MemberStrava::whereNull('deleted_at')->first();
    }

    public function isConfigured(): bool
    {
        return !empty(config('association.strava_client_id'))
            && !empty(config('association.strava_client_secret'));
    }

    /**
     * Test the API connection by calling GET /athlete.
     */
    public function testConnection(): array
    {
        $link = $this->getConnection();
        if (!$link) {
            return ['ok' => false, 'athlete' => null, 'error' => 'Aucun compte connecté'];
        }

        if (!StravaController::refreshToken($link)) {
            return ['ok' => false, 'athlete' => null, 'error' => 'Impossible de rafraîchir le token'];
        }

        $link->refresh();

        try {
            $response = Http::withToken($link->access_token)
                ->get('https://www.strava.com/api/v3/athlete');

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'ok' => true,
                    'athlete' => [
                        'id' => $data['id'],
                        'name' => trim(($data['firstname'] ?? '') . ' ' . ($data['lastname'] ?? '')),
                        'city' => $data['city'] ?? null,
                    ],
                    'error' => null,
                ];
            }

            return [
                'ok' => false,
                'athlete' => null,
                'error' => 'HTTP ' . $response->status() . ' — ' . ($response->json('message') ?? $response->body()),
            ];
        } catch (\Exception $e) {
            return ['ok' => false, 'athlete' => null, 'error' => $e->getMessage()];
        }
    }
}
