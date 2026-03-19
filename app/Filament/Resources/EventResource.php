<?php

namespace App\Filament\Resources;

use App\Enums\EventStatus;
use App\Filament\Resources\EventResource\Pages;
use App\Filament\Resources\EventResource\RelationManagers;
use App\Models\Event;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $modelLabel = 'Événement';

    protected static ?string $pluralModelLabel = 'Événements';

    protected static ?string $navigationLabel = 'Événements';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Détails de l\'événement')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Titre')
                            ->required()
                            ->maxLength(200)
                            ->columnSpanFull(),
                        Forms\Components\RichEditor::make('description')
                            ->label('Description')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('location')
                            ->label('Lieu'),
                        Forms\Components\Select::make('statuscode')
                            ->label('Statut')
                            ->options(collect(EventStatus::cases())->mapWithKeys(fn ($s) => [$s->value => $s->getLabel()]))
                            ->default('N')
                            ->required(),
                        Forms\Components\DateTimePicker::make('starts_at')
                            ->label('Début')
                            ->required(),
                        Forms\Components\DateTimePicker::make('ends_at')
                            ->label('Fin'),
                        Forms\Components\Select::make('chef_peloton_id')
                            ->label('Cheffe de peloton')
                            ->relationship('chefPeloton', 'last_name')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->first_name . ' ' . $record->last_name)
                            ->searchable(['first_name', 'last_name'])
                            ->preload()
                            ->nullable(),
                        Forms\Components\TextInput::make('max_participants')
                            ->label('Places max.')
                            ->numeric()
                            ->minValue(1),
                        Forms\Components\TextInput::make('price')
                            ->label('Prix (CHF)')
                            ->numeric()
                            ->prefix('CHF')
                            ->default(0),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Titre')
                    ->searchable()
                    ->sortable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('starts_at')
                    ->label('Début')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('location')
                    ->label('Lieu')
                    ->searchable(),
                Tables\Columns\TextColumn::make('chefPeloton.first_name')
                    ->label('Cheffe de peloton')
                    ->formatStateUsing(fn ($record) => $record->chefPeloton ? $record->chefPeloton->first_name . ' ' . $record->chefPeloton->last_name : '—')
                    ->sortable(),
                Tables\Columns\TextColumn::make('statuscode')
                    ->label('Statut')
                    ->badge()
                    ->formatStateUsing(fn (EventStatus $state) => $state->getLabel())
                    ->color(fn (EventStatus $state) => $state->getColor())
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Prix')
                    ->money('CHF')
                    ->sortable(),
                Tables\Columns\TextColumn::make('members_count')
                    ->label('Participantes')
                    ->counts('members')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('statuscode')
                    ->label('Statut')
                    ->options(collect(EventStatus::cases())->mapWithKeys(fn ($s) => [$s->value => $s->getLabel()])),
                Tables\Filters\TrashedFilter::make()
                    ->label('Supprimés'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label('Voir'),
                Tables\Actions\EditAction::make()->label('Modifier'),
                Tables\Actions\RestoreAction::make()
                    ->label('Restaurer')
                    ->visible(fn () => auth()->user()->isAdmin()),
                Tables\Actions\ForceDeleteAction::make()
                    ->label('Supprimer définitivement')
                    ->visible(fn () => auth()->user()->isAdmin()),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('Supprimer'),
                    Tables\Actions\RestoreBulkAction::make()
                        ->label('Restaurer')
                        ->visible(fn () => auth()->user()->isAdmin()),
                    Tables\Actions\ForceDeleteBulkAction::make()
                        ->label('Supprimer définitivement')
                        ->visible(fn () => auth()->user()->isAdmin()),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\MembersRelationManager::class,
            RelationManagers\PresencesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEvents::route('/'),
            'create' => Pages\CreateEvent::route('/create'),
            'view' => Pages\ViewEvent::route('/{record}'),
            'edit' => Pages\EditEvent::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
