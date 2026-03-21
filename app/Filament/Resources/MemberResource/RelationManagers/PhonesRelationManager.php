<?php

namespace App\Filament\Resources\MemberResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PhonesRelationManager extends RelationManager
{
    protected static string $relationship = 'phones';

    protected static ?string $title = 'Téléphones';

    protected static ?string $modelLabel = 'Téléphone';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('phone_number')
                    ->label('Numéro')
                    ->tel()
                    ->required()
                    ->maxLength(20)
                    ->rules([
                        fn () => function (string $attribute, $value, \Closure $fail) {
                            $result = \App\Services\PhoneFormatter::format($value);
                            if ($result['error'] !== null) {
                                $fail($result['error']);
                            }
                        },
                    ])
                    ->extraInputAttributes([
                        'x-on:blur' => "if (typeof sgPhoneFormat === 'function') { let r = sgPhoneFormat(\$el.value); if (r.error) { } else { \$el.value = r.formatted; \$dispatch('input', r.formatted) } }",
                    ]),
                Forms\Components\TextInput::make('label')
                    ->label('Type')
                    ->maxLength(40)
                    ->placeholder('Mobile, Domicile...'),
                Forms\Components\Toggle::make('is_whatsapp')
                    ->label('WhatsApp'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('phone_number')
                    ->label('Numéro')
                    ->url(fn ($record) => 'tel:' . $record->phone_number)
                    ->color('primary'),
                Tables\Columns\TextColumn::make('label')
                    ->label('Type')
                    ->placeholder('—'),
                Tables\Columns\IconColumn::make('is_whatsapp')
                    ->label('WA')
                    ->boolean()
                    ->trueIcon('heroicon-s-check-circle')
                    ->falseIcon('')
                    ->trueColor('success'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Ajouter')
                    ->icon('heroicon-o-plus')
                    ->color('primary'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('')
                    ->tooltip('Modifier')
                    ->color('info'),
                Tables\Actions\DeleteAction::make()
                    ->label('')
                    ->tooltip('Supprimer'),
            ])
            ->bulkActions([]);
    }
}
