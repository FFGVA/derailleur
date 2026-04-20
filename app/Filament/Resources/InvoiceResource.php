<?php

namespace App\Filament\Resources;

use App\Enums\InvoiceStatus;
use App\Enums\InvoiceType;
use App\Filament\Forms\PaymentDateForm;
use App\Filament\Resources\InvoiceResource\Pages;
use App\Models\Invoice;
use App\Models\Member;
use App\Services\InvoicePdfService;
use App\Services\InvoiceService;
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
                            ->disabled(),
                        Forms\Components\Select::make('type')
                            ->label('Type')
                            ->options(collect(InvoiceType::cases())->mapWithKeys(fn ($s) => [$s->value => $s->getLabel()]))
                            ->default(InvoiceType::Cotisation->value)
                            ->required()
                            ->live(),
                        Forms\Components\Select::make('statuscode')
                            ->label('Statut')
                            ->options(collect(InvoiceStatus::cases())->mapWithKeys(fn ($s) => [$s->value => $s->getLabel()]))
                            ->default(InvoiceStatus::New->value)
                            ->required(),
                        Forms\Components\TextInput::make('amount')
                            ->label('Total (CHF)')
                            ->numeric()
                            ->prefix('CHF')
                            ->default(config('association.cotisation_annuelle'))
                            ->disabled()
                            ->dehydrated(),
                        Forms\Components\DatePicker::make('payment_date')
                            ->label('Date de paiement')
                            ->displayFormat('d.m.Y'),
                        Forms\Components\TextInput::make('cotisation_year')
                            ->label('Année')
                            ->numeric()
                            ->default(date('Y'))
                            ->visible(fn (Forms\Get $get) => $get('type') === InvoiceType::Cotisation->value),
                        Forms\Components\Select::make('events')
                            ->label('Événements')
                            ->relationship('events', 'title')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->visible(fn (Forms\Get $get) => $get('type') === InvoiceType::Evenement->value),
                        Forms\Components\Textarea::make('notes')
                            ->label('Notes')
                            ->columnSpanFull(),
                    ]),
                Forms\Components\Section::make('Lignes')
                    ->schema([
                        Forms\Components\Repeater::make('lines')
                            ->relationship()
                            ->label('')
                            ->schema([
                                Forms\Components\Textarea::make('description')
                                    ->label('Description')
                                    ->required()
                                    ->rows(1)
                                    ->columnSpan(3),
                                Forms\Components\TextInput::make('amount')
                                    ->label('Montant (CHF)')
                                    ->numeric()
                                    ->prefix('CHF')
                                    ->required(),
                            ])
                            ->columns(4)
                            ->defaultItems(0)
                            ->addActionLabel('Ajouter une ligne')
                            ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get) {
                                $lines = $get('lines') ?? [];
                                $total = collect($lines)->sum('amount');
                                $set('amount', number_format($total, 2, '.', ''));
                            })
                            ->live(),
                    ]),
                Forms\Components\Section::make('Dernière modification')
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
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->heading('Factures')
            ->defaultSort('updated_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('invoice_number')
                    ->label('N° facture')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->formatStateUsing(fn (InvoiceType $state) => $state->getLabel())
                    ->sortable()
                    ->grow(false),
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
                    ->date('d.m.Y')
                    ->sortable(),
                Tables\Columns\IconColumn::make('notes')
                    ->label('')
                    ->icon(fn ($state) => $state ? 'heroicon-s-star' : null)
                    ->color('warning')
                    ->tooltip(fn ($record) => $record->notes)
                    ->grow(false),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Modifiée')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->headerActions([
                Tables\Actions\Action::make('createAutre')
                    ->label('Nouvelle facture')
                    ->icon('heroicon-o-plus')
                    ->color('primary')
                    ->modalHeading('Nouvelle facture')
                    ->modalSubmitActionLabel('Créer')
                    ->modalCancelActionLabel('Annuler')
                    ->form([
                        Forms\Components\Select::make('member_id')
                            ->label('Membre')
                            ->options(
                                Member::whereNull('deleted_at')
                                    ->orderBy('last_name')
                                    ->get()
                                    ->mapWithKeys(fn ($m) => [$m->id => $m->first_name . ' ' . $m->last_name])
                            )
                            ->searchable()
                            ->required(),
                        Forms\Components\Textarea::make('notes')
                            ->label('Notes')
                            ->rows(2),
                        Forms\Components\Repeater::make('lines')
                            ->label('Lignes')
                            ->schema([
                                Forms\Components\Textarea::make('description')
                                    ->label('Description')
                                    ->required()
                                    ->rows(1)
                                    ->columnSpan(3),
                                Forms\Components\TextInput::make('amount')
                                    ->label('Montant (CHF)')
                                    ->numeric()
                                    ->prefix('CHF')
                                    ->required(),
                            ])
                            ->columns(4)
                            ->defaultItems(1)
                            ->addActionLabel('Ajouter une ligne')
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        $member = Member::findOrFail($data['member_id']);
                        $invoice = \App\Services\InvoiceService::createAutre($member, $data['notes'] ?? null);

                        foreach ($data['lines'] as $i => $line) {
                            $invoice->lines()->create([
                                'description' => $line['description'],
                                'amount' => $line['amount'],
                                'sort_order' => $i,
                            ]);
                        }

                        $invoice->recalculateAmount();
                        InvoicePdfService::generate($invoice);

                        \Filament\Notifications\Notification::make()
                            ->title('Facture créée')
                            ->body("Facture {$invoice->invoice_number} créée pour {$member->first_name} {$member->last_name}")
                            ->success()
                            ->send();

                        return redirect(InvoiceResource::getUrl('view', ['record' => $invoice]));
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('statuscode')
                    ->label('Statut')
                    ->multiple()
                    ->options(collect(InvoiceStatus::cases())->mapWithKeys(fn ($s) => [$s->value => $s->getLabel()]))
                    ->default([InvoiceStatus::New->value, InvoiceStatus::Sent->value]),
                Tables\Filters\TrashedFilter::make()
                    ->label('Supprimées'),
            ])
            ->actions([
                Tables\Actions\Action::make('markPaid')
                    ->label('Marquer payée')
                    ->icon('heroicon-o-banknotes')
                    ->color('success')
                    ->modalHeading('Marquer payée')
                    ->modalSubmitActionLabel('OK')
                    ->modalCancelActionLabel('Annuler')
                    ->modalWidth('md')
                    ->form(PaymentDateForm::schema())
                    ->action(function (Invoice $record, array $data) {
                        $date = \DateTime::createFromFormat('d.m.Y', $data['payment_date']);
                        $updates = [
                            'statuscode' => InvoiceStatus::Paid->value,
                            'payment_date' => $date->format('Y-m-d'),
                        ];
                        if (!empty($data['notes'])) {
                            $existing = $record->notes;
                            $updates['notes'] = $existing
                                ? $existing . "\n" . $data['notes']
                                : $data['notes'];
                        }
                        $record->update($updates);
                        // Assign member number on payment
                        Member::assignMemberNumber($record->member);
                        // Extend membership for cotisation invoices
                        \App\Services\InvoicePaymentService::onCotisationPaid($record);
                    })
                    ->visible(fn (Invoice $record) => $record->statuscode !== InvoiceStatus::Paid && $record->statuscode !== InvoiceStatus::Cancelled),
            ])
            ->recordUrl(fn ($record) => InvoiceResource::getUrl('view', ['record' => $record]))
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInvoices::route('/'),
            'view' => Pages\ViewInvoice::route('/{record}'),
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
