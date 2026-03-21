<?php

namespace App\Filament\Widgets;

use App\Models\Member;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ExpiringMemberships extends StatsOverviewWidget
{
    protected static ?int $sort = 0;

    protected static ?string $pollingInterval = null;

    public static function canView(): bool
    {
        // Only show on invoices page
        return request()->is('admin/invoices*');
    }

    protected function getStats(): array
    {
        $endOfNextMonth = now()->addMonth()->endOfMonth();

        $currentYear = (int) date('Y');

        $count = Member::where('statuscode', 'A')
            ->whereNotNull('membership_end')
            ->where('membership_end', '<=', $endOfNextMonth)
            ->whereNull('deleted_at')
            ->whereDoesntHave('invoices', function ($q) use ($currentYear) {
                $q->where('type', 'C')
                    ->where('statuscode', 'P')
                    ->whereNull('deleted_at')
                    ->where('cotisation_year', '>=', $currentYear);
            })
            ->count();

        return [
            Stat::make('Adhésions qui expirent', $count)
                ->description('Fin d\'adhésion ce mois ou avant')
                ->icon('heroicon-o-exclamation-triangle')
                ->color($count > 0 ? 'danger' : 'success')
                ->url(\App\Filament\Pages\Cotisations::getUrl()),
        ];
    }
}
