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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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
                        Forms\Components\DatePicker::make('date_of_birth')
                            ->label('Date de naissance'),
                        Forms\Components\Repeater::make('phones')
                            ->relationship()
                            ->label('Téléphones')
                            ->schema([
                                Forms\Components\TextInput::make('phone_number')
                                    ->label('Numéro')
                                    ->tel()
                                    ->required()
                                    ->maxLength(20),
                                Forms\Components\TextInput::make('label')
                                    ->label('Type')
                                    ->maxLength(40)
                                    ->placeholder('Mobile, Domicile...'),
                                Forms\Components\Toggle::make('is_whatsapp')
                                    ->label('WhatsApp'),
                            ])
                            ->columns(3)
                            ->defaultItems(0)
                            ->addActionLabel('Ajouter un téléphone')
                            ->collapsible()
                            ->columnSpanFull(),
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
                        Forms\Components\Toggle::make('is_invitee')
                            ->label('Invitée (non-membre)'),
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
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\ViewColumn::make('phones_display')
                    ->label('Tél.')
                    ->view('filament.columns.phones'),
                Tables\Columns\TextColumn::make('statuscode')
                    ->label('Statut')
                    ->badge()
                    ->formatStateUsing(fn (MemberStatus $state) => $state->getLabel())
                    ->color(fn (MemberStatus $state) => $state->getColor())
                    ->sortable(),
                Tables\Columns\TextColumn::make('city')
                    ->label('Ville')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('membership_end')
                    ->label('Fin adhésion')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
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
                Tables\Actions\BulkAction::make('downloadVcards')
                    ->label('Télécharger vCards')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                        $records->load('phones');
                        $vcf = $records->map(function ($member) {
                            $lines = [
                                'BEGIN:VCARD',
                                'VERSION:3.0',
                                'N:' . $member->last_name . ';' . $member->first_name . ';;;',
                                'FN:' . $member->first_name . ' ' . $member->last_name,
                                'EMAIL:' . $member->email,
                            ];
                            foreach ($member->phones as $phone) {
                                $type = strtoupper($phone->label ?? 'CELL');
                                $lines[] = 'TEL;TYPE=' . $type . ':' . $phone->phone_number;
                            }
                            if ($member->address) {
                                $lines[] = 'ADR;TYPE=HOME:;;' . str_replace("\n", ' ', $member->address) . ';' . ($member->city ?? '') . ';;' . ($member->postal_code ?? '') . ';' . ($member->country ?? 'CH');
                            }
                            $lines[] = 'END:VCARD';
                            return implode("\r\n", $lines);
                        })->implode("\r\n");

                        return response()->streamDownload(
                            function () use ($vcf) { echo $vcf; },
                            'membres-ffgva.vcf',
                            ['Content-Type' => 'text/vcard']
                        );
                    }),
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
