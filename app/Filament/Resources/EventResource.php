<?php

namespace App\Filament\Resources;

use App\Enums\EventMemberStatus;
use App\Enums\EventStatus;
use App\Enums\EventType;
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
                Forms\Components\Grid::make(3)
                    ->schema([
                        // ── Row 1: Événement + (Infos, Tarifs stacked) ──
                        Forms\Components\Section::make('Événement')
                            ->extraAttributes(['class' => 'ffgva-card-beige'])
                            ->columnSpan(2)
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->label('Titre')
                                    ->required()
                                    ->maxLength(200),
                                Forms\Components\RichEditor::make('description')
                                    ->label('Description'),
                            ]),
                        Forms\Components\Group::make([
                            Forms\Components\Section::make('Infos')
                                ->extraAttributes(['class' => 'ffgva-card-rose'])
                                ->columns(2)
                                ->schema([
                                    Forms\Components\Select::make('event_type')
                                        ->label('Type')
                                        ->options(collect(EventType::cases())->mapWithKeys(fn ($t) => [$t->value => $t->getLabel()])),
                                    Forms\Components\Select::make('statuscode')
                                        ->label('Status Web')
                                        ->options(collect(EventStatus::cases())->mapWithKeys(fn ($s) => [$s->value => $s->getLabel()]))
                                        ->default(EventStatus::Nouveau->value)
                                        ->required(),
                                ]),
                            Forms\Components\Section::make('Tarifs')
                                ->extraAttributes(['class' => 'ffgva-card-rose'])
                                ->schema([
                                    Forms\Components\TextInput::make('price')
                                        ->label('Prix membres')
                                        ->rule('numeric')
                                        ->prefix('CHF')
                                        ->default(0)
                                        ->inlineLabel()
                                        ->extraAttributes(['class' => 'ffgva-amount-field'])
                                        ->extraInputAttributes(['style' => 'text-align: right;']),
                                    Forms\Components\Toggle::make('members_only')
                                        ->label('Exclusif')
                                        ->default(false)
                                        ->live()
                                        ->afterStateUpdated(function (Forms\Set $set, $state) {
                                            $set('price_non_member', $state ? '9999.99' : null);
                                        })
                                        ->inline()
                                        ->inlineLabel(),
                                    Forms\Components\TextInput::make('price_non_member')
                                        ->label('Prix non-membres')
                                        ->rule('nullable')
                                        ->rule('numeric')
                                        ->prefix('CHF')
                                        ->disabled(fn (Forms\Get $get): bool => (bool) $get('members_only'))
                                        ->dehydrated()
                                        ->inlineLabel()
                                        ->extraAttributes(['class' => 'ffgva-amount-field'])
                                        ->extraInputAttributes(['style' => 'text-align: right;']),
                                ]),
                        ])->columnSpan(1),

                        // ── Row 2: Lieu + Dates (aligned horizontally) ──
                        Forms\Components\Section::make('Lieu')
                            ->extraAttributes(['class' => 'ffgva-card-beige'])
                            ->columnSpan(2)
                            ->columns(12)
                            ->schema([
                                Forms\Components\TextInput::make('location')
                                    ->label('Lieu')
                                    ->columnSpan(10),
                                Forms\Components\TextInput::make('max_participants')
                                    ->label('Places')
                                    ->rule('integer')
                                    ->rule('min:1')
                                    ->maxLength(4)
                                    ->columnSpan(2),
                            ]),
                        Forms\Components\Section::make('Dates')
                            ->extraAttributes(['class' => 'ffgva-card-rose'])
                            ->columnSpan(1)
                            ->schema([
                                Forms\Components\DateTimePicker::make('starts_at')
                                    ->label('Début')
                                    ->displayFormat('d.m.Y H:i')
                                    ->required()
                                    ->inlineLabel(),
                                Forms\Components\DateTimePicker::make('ends_at')
                                    ->label('Fin')
                                    ->displayFormat('d.m.Y H:i')
                                    ->inlineLabel(),
                            ]),

                        // ── Row 3: GPX + (Cheffes, Dernière modif stacked) ──
                        Forms\Components\Section::make('GPX et Strava')
                            ->extraAttributes(['class' => 'ffgva-card-beige'])
                            ->columnSpan(2)
                            ->schema([
                                Forms\Components\FileUpload::make('gpx_file')
                                    ->label('Fichier GPX')
                                    ->disk('public')
                                    ->directory('gpx')
                                    ->preserveFilenames()
                                    ->maxSize(5120),
                                Forms\Components\TextInput::make('strava_event_id')
                                    ->label('ID événement Strava')
                                    ->numeric()
                                    ->nullable()
                                    ->helperText('Coller l\'ID depuis l\'URL Strava du group event'),
                                Forms\Components\TextInput::make('strava_route_id')
                                    ->label('ID parcours Strava')
                                    ->numeric()
                                    ->nullable()
                                    ->helperText('Rempli automatiquement lors de la sync')
                                    ->disabled(),
                            ]),
                        Forms\Components\Group::make([
                            Forms\Components\Section::make('Cheffes de peloton')
                                ->extraAttributes(['class' => 'ffgva-card-rose'])
                                ->schema([
                                    Forms\Components\Select::make('chef_ids')
                                        ->hiddenLabel()
                                        ->multiple()
                                        ->options(fn () => \App\Models\Member::whereNull('deleted_at')
                                            ->orderBy('first_name')
                                            ->get()
                                            ->mapWithKeys(fn ($m) => [$m->id => $m->first_name . ' ' . $m->last_name]))
                                        ->searchable()
                                        ->preload(),
                                ]),
                            Forms\Components\Section::make('Dernière modification')
                                ->extraAttributes(['class' => 'ffgva-card-rose'])
                                ->icon('heroicon-o-clock')
                                ->columns(2)
                                ->schema([
                                    Forms\Components\Placeholder::make('updated_at_display')
                                        ->label('Date')
                                        ->content(fn ($record) => $record?->updated_at?->format('d.m.Y H:i') ?? '—'),
                                    Forms\Components\Placeholder::make('modified_by_display')
                                        ->label('Par')
                                        ->content(fn ($record) => $record?->modifiedBy?->name ?? '—'),
                                ])
                                ->hiddenOn('create'),
                        ])->columnSpan(1),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('starts_at', 'asc')
            ->columns([
                Tables\Columns\TextColumn::make('event_type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn (?EventType $state) => $state?->getLabel() ?? '—')
                    ->color(fn (?EventType $state) => $state ? \Filament\Support\Colors\Color::hex($state->getColor()) : 'gray')
                    ->sortable(),
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
                    ->searchable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('chefs_list')
                    ->label('Cheffes')
                    ->state(fn ($record) => $record->chefs->map(fn ($c) => $c->first_name . ' ' . $c->last_name)->join(', ') ?: '—')
                    ->wrap(),
                Tables\Columns\TextColumn::make('statuscode')
                    ->label('Statut')
                    ->badge()
                    ->formatStateUsing(fn (EventStatus $state) => $state->getLabel())
                    ->color(fn (EventStatus $state) => $state->getColor())
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Prix')
                    ->money('CHF', locale: 'de_CH')
                    ->tooltip(fn (Event $record): ?string => $record->members_only ? 'Événement membres' : null)
                    ->extraAttributes(fn (Event $record): array => $record->members_only
                        ? ['style' => 'background-color: #fef2f2;']
                        : [])
                    ->sortable(),
                Tables\Columns\TextColumn::make('active_members_count')
                    ->label('Participantes')
                    ->state(fn ($record) => $record->members()->whereIn('event_member.status', [EventMemberStatus::Inscrit->value, EventMemberStatus::Confirme->value])->count())
                    ->sortable(false),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('actifs')
                    ->label('Événements actifs')
                    ->default(true)
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotIn('statuscode', [EventStatus::Termine->value, EventStatus::Annule->value]),
                        false: fn (Builder $query) => $query->whereIn('statuscode', [EventStatus::Termine->value, EventStatus::Annule->value]),
                        blank: fn (Builder $query) => $query,
                    ),
                Tables\Filters\SelectFilter::make('event_type')
                    ->label('Type')
                    ->options(collect(EventType::cases())->mapWithKeys(fn ($t) => [$t->value => $t->getLabel()])),
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
