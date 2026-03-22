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
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $modelLabel = 'Événement';

    protected static ?string $pluralModelLabel = 'Événements';

    protected static ?string $navigationLabel = 'Événements';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Détails de l\'événement')
                    ->columns(12)
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
                            ->label('Lieu')
                            ->columnSpan(6),
                        Forms\Components\Select::make('statuscode')
                            ->label('Statut')
                            ->options(collect(EventStatus::cases())->mapWithKeys(fn ($s) => [$s->value => $s->getLabel()]))
                            ->default('N')
                            ->required()
                            ->columnSpan(3),
                        Forms\Components\TextInput::make('max_participants')
                            ->label('Places')
                            ->numeric()
                            ->minValue(1)
                            ->columnSpan(3),
                        Forms\Components\TextInput::make('price')
                            ->label('Prix membre')
                            ->numeric()
                            ->prefix('CHF')
                            ->default(0)
                            ->columnSpan(3),
                        Forms\Components\TextInput::make('price_non_member')
                            ->label('Prix non-membre')
                            ->numeric()
                            ->prefix('CHF')
                            ->columnSpan(3),
                        Forms\Components\DateTimePicker::make('starts_at')
                            ->label('Début')
                            ->displayFormat('d.m.Y H:i')
                            ->required()
                            ->columnSpan(3),
                        Forms\Components\DateTimePicker::make('ends_at')
                            ->label('Fin')
                            ->displayFormat('d.m.Y H:i')
                            ->columnSpan(3),
                        Forms\Components\Select::make('chef_peloton_id')
                            ->label('Cheffe de peloton')
                            ->relationship('chefPeloton', 'last_name')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->first_name . ' ' . $record->last_name)
                            ->searchable(['first_name', 'last_name'])
                            ->preload()
                            ->nullable()
                            ->columnSpan(6),
                        Forms\Components\FileUpload::make('gpx_file')
                            ->label('Fichier GPX')
                            ->disk('public')
                            ->directory('gpx')
                            ->preserveFilenames()
                            ->maxSize(5120)
                            ->columnSpan(6),
                        Forms\Components\Actions::make([
                            Forms\Components\Actions\Action::make('delete')
                                ->label('Supprimer')
                                ->icon('heroicon-o-trash')
                                ->color('danger')
                                ->requiresConfirmation()
                                ->action(function ($record) {
                                    if ($record->members()->count() > 0) {
                                        \Filament\Notifications\Notification::make()
                                            ->title('Suppression impossible')
                                            ->body('Cet événement a des participantes. Retirez-les d\'abord.')
                                            ->danger()
                                            ->send();
                                        return;
                                    }
                                    $record->delete();
                                    redirect(EventResource::getUrl('index'));
                                })
                                ->visible(fn (?Model $record) => $record !== null && auth()->user()->isAdmin()),
                        ])->alignEnd()->verticallyAlignEnd()->columnSpanFull(),
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
                    ->dateTime('d.m.Y H:i')
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
                    ->money('CHF', locale: 'de_CH')
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
            ->recordUrl(fn ($record) => EventResource::getUrl('view', ['record' => $record]))
            ->actions([])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\MembersRelationManager::class,
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
