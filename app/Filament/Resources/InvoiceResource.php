<?php

namespace App\Filament\Resources;

use App\Enums\InvoiceStatus;
use App\Filament\Resources\InvoiceResource\Pages;
use App\Models\Invoice;
use App\Models\Member;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $modelLabel = 'Facture';

    protected static ?string $pluralModelLabel = 'Factures';

    protected static ?string $navigationLabel = 'Factures';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Facture')
                    ->columns(4)
                    ->schema([
                        Forms\Components\Select::make('member_id')
                            ->label('Membre')
                            ->relationship('member', 'last_name')
                            ->getOptionLabelFromRecordUsing(fn (Member $r) => $r->first_name . ' ' . $r->last_name)
                            ->searchable(['first_name', 'last_name'])
                            ->preload()
                            ->required()
                            ->columnSpan(2),
                        Forms\Components\TextInput::make('invoice_number')
                            ->label('N° facture')
                            ->disabled()
                            ->columnSpan(2),
                        Forms\Components\Select::make('statuscode')
                            ->label('Statut')
                            ->options(collect(InvoiceStatus::cases())->mapWithKeys(fn ($s) => [$s->value => $s->getLabel()]))
                            ->default('N')
                            ->required(),
                        Forms\Components\TextInput::make('amount')
                            ->label('Montant (CHF)')
                            ->numeric()
                            ->prefix('CHF')
                            ->default(config('ffgva.cotisation_annuelle'))
                            ->required(),
                        Forms\Components\DatePicker::make('payment_date')
                            ->label('Date de paiement'),
                        Forms\Components\Placeholder::make('spacer')->label('')->content(''),
                        Forms\Components\Textarea::make('notes')
                            ->label('Notes')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('updated_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('invoice_number')
                    ->label('N° facture')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('member.last_name')
                    ->label('Nom')
                    ->formatStateUsing(fn ($record) => $record->member->first_name . ' ' . $record->member->last_name)
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Montant')
                    ->money('CHF', locale: 'de_CH')
                    ->sortable(),
                Tables\Columns\TextColumn::make('statuscode')
                    ->label('Statut')
                    ->badge()
                    ->formatStateUsing(fn (InvoiceStatus $state) => $state->getLabel())
                    ->color(fn (InvoiceStatus $state) => $state->getColor())
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_date')
                    ->label('Payée le')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\IconColumn::make('notes')
                    ->label('')
                    ->icon(fn ($state) => $state ? 'heroicon-s-star' : null)
                    ->color('warning')
                    ->tooltip(fn ($record) => $record->notes)
                    ->grow(false),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Modifiée')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('statuscode')
                    ->label('Statut')
                    ->options(collect(InvoiceStatus::cases())->mapWithKeys(fn ($s) => [$s->value => $s->getLabel()])),
                Tables\Filters\TrashedFilter::make()
                    ->label('Supprimées'),
            ])
            ->actions([
                Tables\Actions\Action::make('markPaid')
                    ->label('Marquer payée')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->form([
                        Forms\Components\DatePicker::make('payment_date')
                            ->label('Date de paiement')
                            ->default(now())
                            ->required(),
                    ])
                    ->action(function (Invoice $record, array $data) {
                        $record->update([
                            'statuscode' => 'P',
                            'payment_date' => $data['payment_date'],
                        ]);
                        // Assign member number on payment
                        Member::assignMemberNumber($record->member);
                    })
                    ->visible(fn (Invoice $record) => $record->statuscode !== InvoiceStatus::Paid && $record->statuscode !== InvoiceStatus::Cancelled),
                Tables\Actions\Action::make('download')
                    ->label('')
                    ->tooltip('Télécharger')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    ->action(function (Invoice $record) {
                        $member = $record->member;
                        $nameSlug = str_replace(' ', '_', $member->last_name . '_' . $member->first_name);
                        $nameSlug = preg_replace('/[^a-zA-Z0-9_àâäéèêëïîôùûüçÀÂÄÉÈÊËÏÎÔÙÛÜÇ-]/u', '', $nameSlug);
                        $filename = "ffgva_{$nameSlug}-facture-{$record->invoice_number}.pdf";
                        $storagePath = "invoices/{$filename}";

                        if (\Illuminate\Support\Facades\Storage::exists($storagePath)) {
                            return response()->streamDownload(
                                fn () => print(\Illuminate\Support\Facades\Storage::get($storagePath)),
                                $filename,
                                ['Content-Type' => 'application/pdf']
                            );
                        }

                        // Regenerate if file missing
                        $result = \App\Services\InvoiceService::generate($member);
                        return response()->streamDownload(
                            fn () => print($result['pdf']),
                            $result['filename'],
                            ['Content-Type' => 'application/pdf']
                        );
                    }),
                Tables\Actions\EditAction::make()
                    ->label('')
                    ->tooltip('Modifier')
                    ->color('info'),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInvoices::route('/'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with('member')
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
