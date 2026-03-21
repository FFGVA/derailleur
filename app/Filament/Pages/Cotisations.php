<?php

namespace App\Filament\Pages;

use App\Enums\InvoiceStatus;
use App\Enums\MemberStatus;
use App\Mail\InvoiceMail;
use App\Models\Member;
use App\Services\InvoiceService;
use App\Models\Invoice;
use App\Models\Member as MemberModel;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Support\Facades\Mail;

class Cotisations extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-exclamation-triangle';

    protected static ?string $title = 'Cotisations';

    protected static ?string $navigationLabel = 'Cotisations';

    protected static ?int $navigationSort = 5;

    protected static string $view = 'filament.pages.cotisations';

    protected static bool $shouldRegisterNavigation = false;

    public function table(Table $table): Table
    {
        $endOfNextMonth = now()->addMonth()->endOfMonth();
        $currentYear = (int) date('Y');

        return $table
            ->query(
                Member::query()
                    ->where('statuscode', 'A')
                    ->whereNotNull('membership_end')
                    ->where('membership_end', '<=', $endOfNextMonth)
                    ->whereNull('deleted_at')
                    ->whereDoesntHave('invoices', function ($q) use ($currentYear) {
                        $q->where('type', 'C')
                            ->where('statuscode', 'P')
                            ->whereNull('deleted_at')
                            ->where('cotisation_year', '>=', $currentYear);
                    })
            )
            ->defaultSort('membership_end', 'asc')
            ->columns([
                Tables\Columns\TextColumn::make('membership_end')
                    ->label('Fin adhésion')
                    ->date('d.m.Y')
                    ->sortable()
                    ->color(fn ($record) => $record->membership_end->isPast() ? 'danger' : 'warning'),
                Tables\Columns\TextColumn::make('first_name')
                    ->label('Prénom')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('last_name')
                    ->label('Nom')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('statuscode')
                    ->label('Statut membre')
                    ->badge()
                    ->formatStateUsing(fn (MemberStatus $state) => $state->getLabel())
                    ->color(fn (MemberStatus $state) => $state->getColor()),
                Tables\Columns\TextColumn::make('latest_cotisation_year')
                    ->label('Année')
                    ->state(function ($record) {
                        $invoice = $record->invoices()
                            ->where('type', 'C')
                            ->whereNull('deleted_at')
                            ->orderByDesc('cotisation_year')
                            ->first();
                        return $invoice?->cotisation_year ?? '—';
                    }),
                Tables\Columns\TextColumn::make('latest_cotisation_number')
                    ->label('N° facture')
                    ->state(function ($record) {
                        $invoice = $record->invoices()
                            ->where('type', 'C')
                            ->whereNull('deleted_at')
                            ->orderByDesc('cotisation_year')
                            ->first();
                        return $invoice?->invoice_number ?? '—';
                    })
                    ->color('primary')
                    ->url(function ($record) {
                        $invoice = $record->invoices()
                            ->where('type', 'C')
                            ->whereNull('deleted_at')
                            ->orderByDesc('cotisation_year')
                            ->first();
                        return $invoice ? \App\Filament\Resources\InvoiceResource::getUrl('view', ['record' => $invoice]) : null;
                    }),
                Tables\Columns\TextColumn::make('latest_cotisation_status')
                    ->label('Statut facture')
                    ->badge()
                    ->state(function ($record) {
                        $invoice = $record->invoices()
                            ->where('type', 'C')
                            ->whereNull('deleted_at')
                            ->orderByDesc('cotisation_year')
                            ->first();
                        return $invoice ? $invoice->getRawOriginal('statuscode') : null;
                    })
                    ->formatStateUsing(fn (?string $state) => $state ? InvoiceStatus::from($state)->getLabel() : '—')
                    ->color(fn (?string $state) => $state ? InvoiceStatus::from($state)->getColor() : 'gray')
                    ->action(
                        Tables\Actions\Action::make('goToInvoice')
                            ->action(function ($record) {
                                $invoice = $record->invoices()
                                    ->where('type', 'C')
                                    ->whereNull('deleted_at')
                                    ->orderByDesc('cotisation_year')
                                    ->first();
                                if ($invoice) {
                                    redirect(\App\Filament\Resources\InvoiceResource::getUrl('view', ['record' => $invoice]));
                                }
                            })
                    ),
            ])
            ->recordUrl(fn ($record) => \App\Filament\Resources\MemberResource::getUrl('view', ['record' => $record]))
            ->actions([
                Tables\Actions\Action::make('sendInvoice')
                    ->label('Envoyer facture')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('primary')
                    ->requiresConfirmation()
                    ->modalHeading('Envoyer la facture de cotisation')
                    ->modalDescription(fn ($record) => "Créer et envoyer la facture de cotisation " . date('Y') . " à {$record->first_name} {$record->last_name} ({$record->email}) ?")
                    ->modalSubmitActionLabel('Envoyer')
                    ->visible(function ($record) {
                        $currentYear = (int) date('Y');
                        return !$record->invoices()
                            ->where('type', 'C')
                            ->where('cotisation_year', $currentYear)
                            ->whereNull('deleted_at')
                            ->exists();
                    })
                    ->action(function ($record) {
                        $currentYear = (int) date('Y');

                        // Guard against duplicates
                        if ($record->invoices()->where('type', 'C')->where('cotisation_year', $currentYear)->whereNull('deleted_at')->exists()) {
                            Notification::make()
                                ->title('Facture déjà existante')
                                ->body("Une facture de cotisation {$currentYear} existe déjà pour ce membre.")
                                ->warning()
                                ->send();
                            return;
                        }

                        $result = InvoiceService::createCotisation($record, $currentYear);

                        $invoice = $record->invoices()
                            ->where('invoice_number', $result['invoice_number'])
                            ->first();

                        $qrImage = InvoiceService::generateQrCodeBase64($invoice);

                        Mail::send(new InvoiceMail(
                            invoice: $invoice,
                            pdfContent: $result['pdf'],
                            pdfFilename: $result['filename'],
                            qrImageBase64: $qrImage,
                        ));

                        $invoice->update(['statuscode' => 'E']);

                        Notification::make()
                            ->title('Facture envoyée')
                            ->body("Facture {$result['invoice_number']} envoyée à {$record->email}")
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('markPaid')
                    ->label('Payé')
                    ->icon('heroicon-o-banknotes')
                    ->color('info')
                    ->modalHeading('Marquer payée')
                    ->modalSubmitActionLabel('OK')
                    ->modalCancelActionLabel('Annuler')
                    ->modalWidth('md')
                    ->form([
                        Forms\Components\Grid::make(10)
                            ->schema([
                                Forms\Components\TextInput::make('payment_date')
                                    ->label('Date banque :')
                                    ->placeholder('jj.mm.aaaa')
                                    ->columnSpan(3)
                                    ->required()
                                    ->rule('regex:/^\d{2}\.\d{2}\.\d{4}$/')
                                    ->rule(static function () {
                                        return static function (string $attribute, $value, \Closure $fail) {
                                            if (!preg_match('/^\d{2}\.\d{2}\.\d{4}$/', $value)) {
                                                return;
                                            }
                                            $parsed = \DateTime::createFromFormat('d.m.Y', $value);
                                            if (!$parsed || $parsed->format('d.m.Y') !== $value) {
                                                $fail('Date invalide.');
                                            }
                                        };
                                    })
                                    ->live()
                                    ->afterStateUpdated(fn ($state, Forms\Set $set) => $state),
                            ]),
                        Forms\Components\Textarea::make('notes')
                            ->label('Commentaire')
                            ->rows(2),
                    ])
                    ->visible(function ($record) {
                        $currentYear = (int) date('Y');
                        return $record->invoices()
                            ->where('type', 'C')
                            ->where('cotisation_year', $currentYear)
                            ->whereIn('statuscode', ['N', 'E'])
                            ->whereNull('deleted_at')
                            ->exists();
                    })
                    ->action(function ($record, array $data) {
                        $currentYear = (int) date('Y');
                        $invoice = $record->invoices()
                            ->where('type', 'C')
                            ->where('cotisation_year', $currentYear)
                            ->whereIn('statuscode', ['N', 'E'])
                            ->whereNull('deleted_at')
                            ->first();

                        if (!$invoice) {
                            return;
                        }

                        $date = \DateTime::createFromFormat('d.m.Y', $data['payment_date']);
                        $updates = [
                            'statuscode' => 'P',
                            'payment_date' => $date->format('Y-m-d'),
                        ];
                        if (!empty($data['notes'])) {
                            $existing = $invoice->notes;
                            $updates['notes'] = $existing
                                ? $existing . "\n" . $data['notes']
                                : $data['notes'];
                        }
                        $invoice->update($updates);

                        MemberModel::assignMemberNumber($record);
                        InvoiceService::onCotisationPaid($invoice);

                        Notification::make()
                            ->title('Facture payée')
                            ->body("Facture {$invoice->invoice_number} marquée payée")
                            ->success()
                            ->send();
                    }),
            ]);
    }
}
