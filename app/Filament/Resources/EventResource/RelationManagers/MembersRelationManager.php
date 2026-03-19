<?php

namespace App\Filament\Resources\EventResource\RelationManagers;

use App\Enums\EventMemberStatus;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class MembersRelationManager extends RelationManager
{
    protected static string $relationship = 'members';

    protected static ?string $title = 'Participantes';

    protected static ?string $modelLabel = 'Participante';

    protected static ?string $pluralModelLabel = 'Participantes';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('status')
                    ->label('Statut')
                    ->options(collect(EventMemberStatus::cases())->mapWithKeys(fn ($s) => [$s->value => $s->getLabel()]))
                    ->default('N')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('last_name')
            ->columns([
                Tables\Columns\TextColumn::make('last_name')
                    ->label('Nom')
                    ->sortable(),
                Tables\Columns\TextColumn::make('first_name')
                    ->label('Prénom')
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('E-mail'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->state(fn ($record) => EventMemberStatus::from($record->pivot->status))
                    ->formatStateUsing(fn (EventMemberStatus $state) => $state->getLabel())
                    ->color(fn (EventMemberStatus $state) => $state->getColor()),
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->label('Ajouter une participante')
                    ->preloadRecordSelect()
                    ->form(fn (Tables\Actions\AttachAction $action): array => [
                        $action->getRecordSelect()
                            ->label('Membre'),
                        Forms\Components\Select::make('status')
                            ->label('Statut')
                            ->options(collect(EventMemberStatus::cases())->mapWithKeys(fn ($s) => [$s->value => $s->getLabel()]))
                            ->default('N')
                            ->required(),
                    ]),
            ])
            ->actions([
                Tables\Actions\DetachAction::make()->label('Retirer'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make()->label('Retirer'),
                ]),
            ]);
    }
}
