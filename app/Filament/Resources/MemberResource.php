<?php

namespace App\Filament\Resources;

use App\Enums\MemberStatus;
use App\Filament\Resources\MemberResource\Pages;
use App\Filament\Resources\MemberResource\RelationManagers;
use App\Models\Member;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MemberResource extends Resource
{
    protected static ?string $model = Member::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $modelLabel = 'Membre';

    protected static ?string $pluralModelLabel = 'Membres';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Membres';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations personnelles')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('first_name')
                            ->label('Prénom')
                            ->required()
                            ->maxLength(40),
                        Forms\Components\TextInput::make('last_name')
                            ->label('Nom')
                            ->required()
                            ->maxLength(60),
                        Forms\Components\TextInput::make('email')
                            ->label('E-mail')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true),
                        Forms\Components\DatePicker::make('date_of_birth')
                            ->label('Date de naissance')
                            ->displayFormat('d.m.Y'),
                    ]),
                Forms\Components\Section::make('Adhésion')
                    ->columns(4)
                    ->schema([
                        Forms\Components\Select::make('statuscode')
                            ->label('Statut')
                            ->options(collect(MemberStatus::cases())->mapWithKeys(fn ($s) => [$s->value => $s->getLabel()]))
                            ->default('D')
                            ->required()
                            ->columnSpan(2),
                        Forms\Components\DatePicker::make('membership_start')
                            ->label('Début adhésion')
                            ->displayFormat('d.m.Y'),
                        Forms\Components\DatePicker::make('membership_end')
                            ->label('Fin adhésion')
                            ->displayFormat('d.m.Y'),
                        Forms\Components\Textarea::make('notes')
                            ->label('Notes')
                            ->columnSpanFull(),
                    ]),
                Forms\Components\Section::make('Téléphones')
                    ->schema([
                        Forms\Components\Repeater::make('phones')
                            ->relationship()
                            ->label('')
                            ->schema([
                                Forms\Components\TextInput::make('phone_number')
                                    ->label('Numéro')
                                    ->tel()
                                    ->required()
                                    ->maxLength(20)
                                    ->columnSpan(2),
                                Forms\Components\Select::make('label')
                                    ->label('Type')
                                    ->options(\App\Enums\PhoneLabel::class)
                                    ->default('Mobile')
                                    ->columnSpan(2),
                                Forms\Components\Toggle::make('is_whatsapp')
                                    ->label('WhatsApp')
                                    ->inline(false)
                                    ->columnSpan(1),
                            ])
                            ->columns(5)
                            ->defaultItems(0)
                            ->addActionLabel('Ajouter un téléphone')
                            ->reorderable()
                            ->orderColumn('sort_order'),
                    ]),
                Forms\Components\Section::make('Adresse')
                    ->columns(20)
                    ->schema([
                        Forms\Components\Textarea::make('address')
                            ->label('Adresse')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('postal_code')
                            ->label('NPA')
                            ->maxLength(10)
                            ->columnSpan(2),
                        Forms\Components\TextInput::make('city')
                            ->label('Ville')
                            ->columnSpan(16),
                        Forms\Components\TextInput::make('country')
                            ->label('Pays')
                            ->default('CH')
                            ->maxLength(2)
                            ->columnSpan(2),
                    ]),
                Forms\Components\Section::make('Réseaux sociaux')
                    ->icon('heroicon-o-globe-alt')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('metadata.instagram')
                            ->label('Instagram')
                            ->prefix(new \Illuminate\Support\HtmlString('<img src="' . asset('images/instagram-logo.svg') . '" style="width:1.25rem;height:1.25rem;">'))
                            ->placeholder('nom_utilisateur')
                            ->maxLength(30)
                            ->dehydrateStateUsing(fn ($state) => $state ? ltrim($state, '@') : null),
                        Forms\Components\TextInput::make('metadata.strava')
                            ->label('Strava')
                            ->prefix(new \Illuminate\Support\HtmlString('<img src="' . asset('images/strava-logo.svg') . '" style="width:1.25rem;height:1.25rem;">'))
                            ->placeholder('nom_utilisateur')
                            ->maxLength(60),
                    ]),
                Forms\Components\Section::make('Métadonnées')
                    ->schema([
                        Forms\Components\KeyValue::make('metadata')
                            ->label('')
                            ->keyLabel('Clé')
                            ->valueLabel('Valeur')
                            ->addActionLabel('Ajouter')
                            ->reorderable(),
                    ])
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('statuscode')
                    ->label('')
                    ->formatStateUsing(fn () => '⬤')
                    ->color(fn (MemberStatus $state) => $state->getColor())
                    ->sortable()
                    ->grow(false)
                    ->alignCenter()
                    ->tooltip(fn (MemberStatus $state) => $state->getLabel()),
                Tables\Columns\TextColumn::make('first_name')
                    ->label('Prénom')
                    ->searchable()
                    ->sortable()
                    ->grow(false),
                Tables\Columns\TextColumn::make('last_name')
                    ->label('Nom')
                    ->searchable()
                    ->sortable()
                    ->grow(false),
                Tables\Columns\ViewColumn::make('phones_display')
                    ->label('Tél.')
                    ->view('filament.columns.phones')
                    ->grow(false)
                    ->alignStart()
                    ->visibleFrom('md'),
                Tables\Columns\TextColumn::make('email')
                    ->label('E-mail')
                    ->searchable()
                    ->sortable()
                    ->url(fn ($record) => 'mailto:' . $record->email)
                    ->color('primary')
                    ->grow(false)
                    ->visibleFrom('md'),
                Tables\Columns\TextColumn::make('metadata.instagram')
                    ->label('Instagram')
                    ->grow(false)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('member_number')
                    ->label('N°')
                    ->sortable()
                    ->searchable()
                    ->grow(false)
                    ->alignEnd()
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('city')
                    ->label('Ville')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('membership_end')
                    ->label('Fin adhésion')
                    ->date('d.m.Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('updated_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('statuscode')
                    ->label('Statut')
                    ->options(collect(MemberStatus::cases())->mapWithKeys(fn ($s) => [$s->value => $s->getLabel()])),
                Tables\Filters\SelectFilter::make('city')
                    ->label('Ville')
                    ->options(fn () => Member::query()->whereNotNull('city')->distinct()->pluck('city', 'city')->toArray()),
                Tables\Filters\TernaryFilter::make('is_invitee')
                    ->label('Invitées')
                    ->trueLabel('Invitées seulement')
                    ->falseLabel('Membres seulement'),
                Tables\Filters\TrashedFilter::make()
                    ->label('Supprimés'),
            ])
            ->recordUrl(fn ($record) => MemberResource::getUrl('view', ['record' => $record]))
            ->actions([])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMembers::route('/'),
            'create' => Pages\CreateMember::route('/create'),
            'view' => Pages\ViewMember::route('/{record}'),
            'edit' => Pages\EditMember::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with('phones')
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
