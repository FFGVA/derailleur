<?php

namespace App\Filament\Resources\EventResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PresencesRelationManager extends RelationManager
{
    protected static string $relationship = 'members';

    protected static ?string $title = 'Présences';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('last_name')
            ->columns([
                Tables\Columns\TextColumn::make('last_name')
                    ->label('Nom')
                    ->sortable(),
                Tables\Columns\TextColumn::make('first_name')
                    ->label('Prénom'),
                Tables\Columns\IconColumn::make('pivot.present')
                    ->label('Présente')
                    ->state(fn ($record) => $record->pivot->present)
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
            ])
            ->actions([
                Tables\Actions\Action::make('markPresent')
                    ->label('Présente')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(fn ($record) => $record->pivot->update(['present' => true]))
                    ->visible(fn ($record) => !$record->pivot->present),
                Tables\Actions\Action::make('markAbsent')
                    ->label('Absente')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->action(fn ($record) => $record->pivot->update(['present' => false]))
                    ->visible(fn ($record) => $record->pivot->present !== false),
            ]);
    }
}
