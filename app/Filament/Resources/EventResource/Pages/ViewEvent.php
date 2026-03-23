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
                                        ->formatStateUsing(fn (?string $state) => $state ? clean($state) : null)
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
                                    Components\TextEntry::make('chefs_display')
                                        ->label('Cheffes de peloton')
                                        ->icon('heroicon-o-star')
                                        ->html()
                                        ->state(function ($record) {
                                            $chefs = $record->chefs;
                                            if ($chefs->isEmpty()) {
                                                return null;
                                            }
                                            return $chefs->map(fn ($c) =>
                                                '<a href="' . \App\Filament\Resources\MemberResource::getUrl('view', ['record' => $c]) . '" class="text-primary-600 dark:text-primary-400 hover:underline">'
                                                . e($c->first_name . ' ' . $c->last_name)
                                                . '</a>'
                                            )->join('<br>');
                                        })
                                        ->placeholder('—'),
                                ]),
                            Components\Section::make('Strava')
                                ->schema([
                                    Components\TextEntry::make('strava_event_id')
                                        ->label('Événement Strava')
                                        ->icon('heroicon-o-link')
                                        ->placeholder('Non lié'),
                                    Components\TextEntry::make('strava_route_id')
                                        ->label('Parcours Strava')
                                        ->icon('heroicon-o-map')
                                        ->placeholder('—'),
                                ])
                                ->collapsible()
                                ->collapsed(fn ($record) => !$record->strava_event_id),
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
