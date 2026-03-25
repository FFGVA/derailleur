<?php

namespace App\Filament\Widgets;

use App\Models\Invoice;
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

        $openAmount = Invoice::whereIn('statuscode', ['N', 'E'])
            ->whereNull('deleted_at')
            ->sum('amount');

        return [
            Stat::make('Factures ouvertes', 'CHF ' . number_format((float) $openAmount, 2, '.', ''))
                ->description('Montant total non payé')
                ->icon('heroicon-o-banknotes')
                ->color($openAmount > 0 ? 'warning' : 'success'),
            Stat::make('Adhésions qui expirent', $count)
                ->description('Fin d\'adhésion ce mois ou avant')
                ->icon('heroicon-o-exclamation-triangle')
                ->color($count > 0 ? 'danger' : 'success')
                ->url(\App\Filament\Pages\Cotisations::getUrl()),
        ];
    }
}
