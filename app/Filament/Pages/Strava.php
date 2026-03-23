<?php

namespace App\Filament\Pages;

use App\Models\Event;
use App\Models\MemberStrava;
use Filament\Pages\Page;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components;

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
        abort_unless(config('ffgva.strava_enabled', false), 404);
    }

    public function getLinkedEventsCount(): int
    {
        return Event::whereNotNull('strava_event_id')
            ->whereNull('deleted_at')
            ->count();
    }

    public function getLinkedMembersCount(): int
    {
        return MemberStrava::whereNull('deleted_at')->count();
    }

    public function getLinkedEvents(): \Illuminate\Support\Collection
    {
        return Event::whereNotNull('strava_event_id')
            ->whereNull('deleted_at')
            ->orderBy('starts_at', 'desc')
            ->get();
    }

    public function getLinkedMembers(): \Illuminate\Support\Collection
    {
        return MemberStrava::with('member')
            ->whereNull('deleted_at')
            ->get();
    }
}
