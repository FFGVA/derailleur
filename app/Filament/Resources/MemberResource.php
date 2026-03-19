<?php

namespace App\Filament\Resources;

use App\Enums\MemberStatus;
use App\Filament\Resources\MemberResource\Pages;
use App\Models\Member;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MemberResource extends Resource
{
    protected static ?string $model = Member::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $modelLabel = 'Membre';

    protected static ?string $pluralModelLabel = 'Membres';

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
                        Forms\Components\TextInput::make('phone')
                            ->label('Téléphone')
                            ->tel()
                            ->maxLength(20),
                        Forms\Components\DatePicker::make('date_of_birth')
                            ->label('Date de naissance'),
                    ]),
                Forms\Components\Section::make('Adresse')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Textarea::make('address')
                            ->label('Adresse')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('postal_code')
                            ->label('Code postal')
                            ->maxLength(10),
                        Forms\Components\TextInput::make('city')
                            ->label('Ville'),
                        Forms\Components\TextInput::make('country')
                            ->label('Pays')
                            ->default('CH')
                            ->maxLength(2),
                    ]),
                Forms\Components\Section::make('Adhésion')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('statuscode')
                            ->label('Statut')
                            ->options(collect(MemberStatus::cases())->mapWithKeys(fn ($s) => [$s->value => $s->getLabel()]))
                            ->default('D')
                            ->required(),
                        Forms\Components\DatePicker::make('membership_start')
                            ->label('Début d\'adhésion'),
                        Forms\Components\DatePicker::make('membership_end')
                            ->label('Fin d\'adhésion'),
                        Forms\Components\Textarea::make('notes')
                            ->label('Notes')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('last_name')
                    ->label('Nom')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('first_name')
                    ->label('Prénom')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('E-mail')
                    ->searchable(),
                Tables\Columns\TextColumn::make('statuscode')
                    ->label('Statut')
                    ->badge()
                    ->formatStateUsing(fn (MemberStatus $state) => $state->getLabel())
                    ->color(fn (MemberStatus $state) => $state->getColor())
                    ->sortable(),
                Tables\Columns\TextColumn::make('city')
                    ->label('Ville')
                    ->sortable(),
                Tables\Columns\TextColumn::make('membership_end')
                    ->label('Fin d\'adhésion')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('statuscode')
                    ->label('Statut')
                    ->options(collect(MemberStatus::cases())->mapWithKeys(fn ($s) => [$s->value => $s->getLabel()])),
                Tables\Filters\SelectFilter::make('city')
                    ->label('Ville')
                    ->options(fn () => Member::query()->whereNotNull('city')->distinct()->pluck('city', 'city')->toArray()),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Modifier'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('Supprimer'),
                ]),
            ]);
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
            'edit' => Pages\EditMember::route('/{record}/edit'),
        ];
    }
}
