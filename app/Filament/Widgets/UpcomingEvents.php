<?php

namespace App\Filament\Widgets;

use App\Models\Event;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Collection;

class UpcomingEvents extends Widget
{
    protected static string $view = 'filament.widgets.upcoming-events';

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 2;

    public function getEvents(): Collection
    {
        $today = now()->startOfDay();

        return Event::query()
            ->where('statuscode', '!=', 'X')
            ->where(function ($q) use ($today) {
                $q->where(function ($q2) use ($today) {
                    // Has end_date: show if end_date >= today
                    $q2->whereNotNull('ends_at')
                        ->whereDate('ends_at', '>=', $today);
                })->orWhere(function ($q2) use ($today) {
                    // No end_date: show if starts_at >= today
                    $q2->whereNull('ends_at')
                        ->whereDate('starts_at', '>=', $today);
                });
            })
            ->orderBy('starts_at')
            ->get();
    }
}
