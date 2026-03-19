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
                    ->searchable(),
                Tables\Columns\TextColumn::make('phones_display')
                    ->label('Téléphones')
                    ->state(function (Member $record) {
                        return $record->phones->map(function ($phone) {
                            $tel = '<a href="tel:' . e($phone->phone_number) . '" class="text-primary-600 hover:underline">' . e($phone->phone_number) . '</a>';
                            if ($phone->is_whatsapp) {
                                $waNumber = preg_replace('/[^0-9+]/', '', $phone->phone_number);
                                if (!str_starts_with($waNumber, '+')) $waNumber = '+41' . ltrim($waNumber, '0');
                                $waNumber = preg_replace('/[^0-9]/', '', $waNumber);
                                $tel .= ' <a href="https://wa.me/' . $waNumber . '" target="_blank" title="WhatsApp"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#25D366" class="inline-block w-4 h-4"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.116.553 4.103 1.519 5.832L0 24l6.335-1.652A11.943 11.943 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.75c-1.97 0-3.836-.53-5.445-1.476l-.39-.232-3.758.98.998-3.648-.254-.404A9.71 9.71 0 012.25 12C2.25 6.615 6.615 2.25 12 2.25S21.75 6.615 21.75 12 17.385 21.75 12 21.75z"/></svg></a>';
                            }
                            return $tel;
                        })->implode('<br>');
                    })
                    ->html(),
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
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
