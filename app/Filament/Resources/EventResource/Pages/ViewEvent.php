<?php

namespace App\Filament\Resources\EventResource\Pages;

use App\Enums\EventStatus;
use App\Enums\EventType;
use App\Filament\Resources\EventResource;
use App\Filament\Resources\EventResource\RelationManagers;
use Filament\Actions;
use Filament\Forms;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components;
use Filament\Resources\Pages\ViewRecord;

class ViewEvent extends ViewRecord
{
    protected static string $resource = EventResource::class;

    public function getTitle(): string
    {
        return $this->record->title;
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Components\Grid::make(3)
                    ->schema([
                        Components\Group::make([
                            Components\Section::make()
                                ->schema([
                                    Components\TextEntry::make('description')
                                        ->label('')
                                        ->html()
                                        ->placeholder('Pas de description'),
                                ])
                                ->hidden(fn ($record) => empty($record->description)),

                            Components\Section::make()
                                ->columns(2)
                                ->schema([
                                    Components\TextEntry::make('starts_at')
                                        ->label('Début')
                                        ->icon('heroicon-o-calendar-days')
                                        ->dateTime('d.m.Y à H:i'),
                                    Components\TextEntry::make('ends_at')
                                        ->label('Fin')
                                        ->icon('heroicon-o-clock')
                                        ->dateTime('d.m.Y à H:i')
                                        ->placeholder('—'),
                                    Components\TextEntry::make('location')
                                        ->label('Lieu')
                                        ->icon('heroicon-o-map-pin')
                                        ->url(fn ($record) => $record->location ? 'https://maps.google.com/?q=' . urlencode($record->location) : null)
                                        ->openUrlInNewTab()
                                        ->color('primary')
                                        ->placeholder('—')
                                        ->columnSpanFull(),
                                    Components\TextEntry::make('gpx_file')
                                        ->label('Fichier GPX')
                                        ->icon('heroicon-o-map')
                                        ->state(fn ($record) => $record->gpx_file ? basename($record->gpx_file) : null)
                                        ->url(fn ($record) => $record->gpx_file ? asset('storage/' . $record->gpx_file) : null)
                                        ->openUrlInNewTab()
                                        ->color('primary')
                                        ->placeholder('—')
                                        ->columnSpanFull(),
                                ]),
                        ])->columnSpan(2),

                        Components\Group::make([
                            Components\Section::make()
                                ->columns(2)
                                ->schema([
                                    Components\TextEntry::make('event_type')
                                        ->label('Type')
                                        ->badge()
                                        ->formatStateUsing(fn (?EventType $state) => $state?->getLabel() ?? '—')
                                        ->color(fn (?EventType $state) => $state ? \Filament\Support\Colors\Color::hex($state->getColor()) : 'gray'),
                                    Components\TextEntry::make('statuscode')
                                        ->label('Statut')
                                        ->badge()
                                        ->formatStateUsing(fn (EventStatus $state) => $state->getLabel())
                                        ->color(fn (EventStatus $state) => $state->getColor()),
                                    Components\TextEntry::make('members_count')
                                        ->label('Participantes')
                                        ->state(fn ($record) => $record->members()->count())
                                        ->icon('heroicon-o-user-group'),
                                    Components\TextEntry::make('price')
                                        ->label('Prix membre')
                                        ->money('CHF', locale: 'de_CH')
                                        ->icon('heroicon-o-banknotes'),
                                    Components\TextEntry::make('price_non_member')
                                        ->label('Prix non-membre')
                                        ->money('CHF', locale: 'de_CH')
                                        ->icon('heroicon-o-banknotes')
                                        ->placeholder('—'),
                                    Components\TextEntry::make('max_participants')
                                        ->label('Places max.')
                                        ->placeholder('—')
                                        ->icon('heroicon-o-users'),
                                ]),
                            Components\Section::make()
                                ->schema([
                                    Components\TextEntry::make('chefPeloton.full_name')
                                        ->label('Cheffe de peloton')
                                        ->icon('heroicon-o-star')
                                        ->state(fn ($record) => $record->chefPeloton
                                            ? $record->chefPeloton->first_name . ' ' . $record->chefPeloton->last_name
                                            : null)
                                        ->url(fn ($record) => $record->chefPeloton
                                            ? \App\Filament\Resources\MemberResource::getUrl('view', ['record' => $record->chefPeloton])
                                            : null)
                                        ->color('primary')
                                        ->placeholder('—'),
                                ]),
                        ])->columnSpan(1),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('Modifier')
                ->icon('heroicon-o-pencil-square')
                ->color('info'),
        ];
    }

    public function getRelationManagers(): array
    {
        return [
            RelationManagers\MembersRelationManager::class,
        ];
    }
}
