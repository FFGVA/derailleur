<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\EventResource;
use App\Filament\Resources\InvoiceResource;
use App\Filament\Resources\MemberResource;
use App\Models\Event;
use App\Models\Invoice;
use App\Models\Member;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $plannedEvents = Event::whereIn('statuscode', ['N', 'P'])
            ->where('starts_at', '>=', now()->startOfDay())
            ->whereNull('deleted_at')
            ->count();

        $toClose = Event::where('statuscode', 'P')
            ->where('starts_at', '<', now()->startOfDay())
            ->whereNull('deleted_at')
            ->count();

        $totalEvents = Event::whereNull('deleted_at')->count();

        $unpaidTotal = Invoice::whereIn('statuscode', ['N', 'E'])
            ->sum('amount');

        $activeMembers = Member::where('statuscode', 'A')
            ->whereNull('deleted_at')
            ->count();

        $pendingAdhesions = Member::whereNull('deleted_at')
            ->where(function ($q) {
                $q->where('statuscode', 'P')
                    ->orWhere(function ($q2) {
                        $q2->where('statuscode', 'N')
                            ->whereNotNull('membership_requested_at');
                    });
            })
            ->count();

        return [
            Stat::make('Événements', $plannedEvents . ' planifiés (' . $totalEvents . ')')
                ->description($toClose > 0 ? $toClose . ' à clôturer' : 'Tous à jour')
                ->icon('heroicon-o-calendar-days')
                ->color($toClose > 0 ? 'warning' : 'success')
                ->url(EventResource::getUrl('index')),
            Stat::make('Montants ouverts', 'CHF ' . number_format($unpaidTotal, 2, '.', "'"))
                ->description('Factures non payées')
                ->icon('heroicon-o-banknotes')
                ->color('warning')
                ->url(InvoiceResource::getUrl('index')),
            Stat::make('Membres actives', $activeMembers)
                ->description('Statut actif')
                ->icon('heroicon-o-users')
                ->color('success')
                ->url(MemberResource::getUrl('index')),
            Stat::make('Demandes d\'adhésion', $pendingAdhesions)
                ->description($pendingAdhesions > 0 ? 'En attente' : 'Aucune')
                ->icon('heroicon-o-user-plus')
                ->color($pendingAdhesions > 0 ? 'warning' : 'success')
                ->url(MemberResource::getUrl('index', [
                    'tableFilters' => [
                        'membership_requested' => ['value' => '1'],
                    ],
                ])),
        ];
    }
}
