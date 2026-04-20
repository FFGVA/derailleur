<?php

namespace App\Filament\Forms;

use Filament\Forms\Components;

class PaymentDateForm
{
    /**
     * Reusable form schema for "mark invoice as paid" modals.
     * Returns payment_date (Swiss format dd.mm.yyyy) + optional notes.
     */
    public static function schema(): array
    {
        return [
            Components\Grid::make(10)
                ->schema([
                    Components\TextInput::make('payment_date')
                        ->label('Date banque :')
                        ->placeholder('jj.mm.aaaa')
                        ->columnSpan(3)
                        ->required()
                        ->rule('regex:/^\d{2}\.\d{2}\.\d{4}$/')
                        ->rule(static function () {
                            return static function (string $attribute, $value, \Closure $fail) {
                                if (! preg_match('/^\d{2}\.\d{2}\.\d{4}$/', $value)) {
                                    return;
                                }
                                $parsed = \DateTime::createFromFormat('d.m.Y', $value);
                                if (! $parsed || $parsed->format('d.m.Y') !== $value) {
                                    $fail('Date invalide.');
                                }
                            };
                        })
                        ->live()
                        ->afterStateUpdated(fn ($state) => $state),
                ]),
            Components\Textarea::make('notes')
                ->label('Commentaire')
                ->rows(2),
        ];
    }

    /**
     * Parse a Swiss-format date string from the payment form.
     */
    public static function parseDate(string $value): \DateTime
    {
        return \DateTime::createFromFormat('d.m.Y', $value);
    }
}
