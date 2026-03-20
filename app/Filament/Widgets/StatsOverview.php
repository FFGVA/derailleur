<?php

namespace App\Filament\Widgets;

use App\Models\Invoice;
use App\Models\Member;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $unpaidTotal = Invoice::whereIn('statuscode', ['N', 'E'])
            ->sum('amount');

        $activeMembers = Member::where('statuscode', 'A')
            ->whereNull('deleted_at')
            ->count();

        return [
            Stat::make('Montants ouverts', 'CHF ' . number_format($unpaidTotal, 2, '.', "'"))
                ->description('Factures non payées')
                ->icon('heroicon-o-banknotes')
                ->color('warning'),
            Stat::make('Membres actives', $activeMembers)
                ->description('Statut actif')
                ->icon('heroicon-o-users')
                ->color('success'),
        ];
    }
}
