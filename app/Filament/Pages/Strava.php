<?php

namespace App\Filament\Pages;

use App\Models\Event;
use App\Models\Member;
use App\Models\MemberStrava;
use Filament\Pages\Page;
use Filament\Notifications\Notification;

class Strava extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-link';

    protected static ?string $title = 'Strava';

    protected static ?string $navigationLabel = 'Strava';

    protected static ?int $navigationSort = 6;

    protected static string $view = 'filament.pages.strava';

    public static function shouldRegisterNavigation(): bool
    {
        return config('ffgva.strava_enabled', false);
    }

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

    public function getLinkedEventsCount(): int
    {
        return Event::whereNotNull('strava_event_id')
            ->whereNull('deleted_at')
            ->count();
    }

    public function getStravaAccounts(): \Illuminate\Support\Collection
    {
        return MemberStrava::with(['member', 'user'])
            ->whereNull('deleted_at')
            ->get();
    }

    public function getLinkedEvents(): \Illuminate\Support\Collection
    {
        return Event::whereNotNull('strava_event_id')
            ->whereNull('deleted_at')
            ->orderBy('starts_at', 'desc')
            ->get();
    }

    public function getMembers(): \Illuminate\Support\Collection
    {
        return Member::whereNull('deleted_at')
            ->orderBy('last_name')
            ->get();
    }

    public function isConfigured(): bool
    {
        return !empty(config('ffgva.strava_client_id'))
            && !empty(config('ffgva.strava_client_secret'));
    }
}
