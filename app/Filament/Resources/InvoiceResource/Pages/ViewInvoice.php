<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Enums\InvoiceStatus;
use App\Enums\InvoiceType;
use App\Filament\Resources\InvoiceResource;
use App\Filament\Resources\MemberResource;
use App\Models\Member;
use App\Services\InvoicePaymentService;
use App\Services\InvoiceService;
use Filament\Actions;
use Filament\Forms;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Storage;

class ViewInvoice extends ViewRecord
{
    protected static string $resource = InvoiceResource::class;

    public function getTitle(): string
    {
        return 'Facture ' . $this->record->invoice_number;
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                // Member card
                Components\Section::make()
                    ->schema([
                        Components\Grid::make(3)
                            ->schema([
                                Components\TextEntry::make('member.full_name')
                                    ->label('Membre')
                                    ->state(fn ($record) => $record->member->first_name . ' ' . $record->member->last_name)
                                    ->icon('heroicon-o-user')
                                    ->url(fn ($record) => MemberResource::getUrl('view', ['record' => $record->member]))
                                    ->color('primary')
                                    ->weight('bold'),
                                Components\TextEntry::make('member.email')
                                    ->label('E-mail')
                                    ->icon('heroicon-o-envelope')
                                    ->state(fn ($record) => $record->member->email),
                                Components\TextEntry::make('member.address_display')
                                    ->label('Adresse')
                                    ->state(function ($record) {
                                        $m = $record->member;
                                        $parts = [];
                                        if ($m->address) $parts[] = $m->address;
                                        $line2 = trim(($m->postal_code ?? '') . ' ' . ($m->city ?? ''));
                                        if ($line2) $parts[] = $line2;
                                        return implode(', ', $parts) ?: '—';
                                    }),
                            ]),
                    ]),

                // Invoice header
                Components\Grid::make(3)
                    ->schema([
                        Components\Group::make([
                            Components\Section::make()
                                ->columns(2)
                                ->schema([
                                    Components\TextEntry::make('invoice_number')
                                        ->label('N° facture')
                                        ->weight('bold')
                                        ->size('lg'),
                                    Components\TextEntry::make('type')
                                        ->label('Type')
                                        ->formatStateUsing(fn (InvoiceType $state) => $state->getLabel()),
                                    Components\TextEntry::make('statuscode')
                                        ->label('Statut')
                                        ->badge()
                                        ->formatStateUsing(fn (InvoiceStatus $state) => $state->getLabel())
                                        ->color(fn (InvoiceStatus $state) => $state->getColor()),
                                    Components\TextEntry::make('payment_date')
                                        ->label('Payée le')
                                        ->date('d.m.Y')
                                        ->placeholder('—'),
                                    Components\TextEntry::make('cotisation_year')
                                        ->label('Année cotisation')
                                        ->placeholder('—')
                                        ->hidden(fn ($record) => $record->getRawOriginal('type') !== InvoiceType::Cotisation->value),
                                    Components\TextEntry::make('notes')
                                        ->label('Notes')
                                        ->placeholder('—')
                                        ->columnSpanFull()
                                        ->hidden(fn ($record) => empty($record->notes)),
                                ]),
                        ])->columnSpan(2),

                        Components\Group::make([
                            Components\Section::make()
                                ->schema([
                                    Components\TextEntry::make('amount')
                                        ->label('Total')
                                        ->money('CHF', locale: 'de_CH')
                                        ->size('lg')
                                        ->weight('bold'),
                                    Components\Actions::make([
                                        Components\Actions\Action::make('downloadPdf')
                                            ->label('PDF')
                                            ->icon('heroicon-o-arrow-down-tray')
                                            ->color('primary')
                                            ->action(function () {
                                                $record = $this->record;

                                                if ($record->pdf_filename && Storage::exists('invoices/' . $record->pdf_filename)) {
                                                    return response()->streamDownload(
                                                        fn () => print(Storage::get('invoices/' . $record->pdf_filename)),
                                                        $record->pdf_filename,
                                                        ['Content-Type' => 'application/pdf']
                                                    );
                                                }

                                                $result = \App\Services\InvoiceService::generatePdf($record);
                                                return response()->streamDownload(
                                                    fn () => print($result['pdf']),
                                                    $result['filename'],
                                                    ['Content-Type' => 'application/pdf']
                                                );
                                            }),
                                        Components\Actions\Action::make('sendEmail')
                                            ->label('Envoyer')
                                            ->icon('heroicon-o-envelope')
                                            ->color('success')
                                            ->requiresConfirmation()
                                            ->modalHeading('Envoyer la facture par email')
                                            ->modalDescription(fn () => 'La facture sera envoyée à ' . $this->record->member->email)
                                            ->action(function () {
                                                $record = $this->record->load(['member', 'lines']);
                                                $member = $record->member;

                                                // Get or generate PDF
                                                $filename = $record->pdf_filename;
                                                if ($filename && Storage::exists('invoices/' . $filename)) {
                                                    $pdfContent = Storage::get('invoices/' . $filename);
                                                } else {
                                                    $result = \App\Services\InvoiceService::generatePdf($record);
                                                    $pdfContent = $result['pdf'];
                                                    $filename = $result['filename'];
                                                }

                                                // Generate QR code for email
                                                $qrBase64 = \App\Services\InvoiceService::generateQrCodeBase64($record);

                                                \Illuminate\Support\Facades\Mail::send(
                                                    new \App\Mail\InvoiceMail($record, $pdfContent, $filename, $qrBase64)
                                                );

                                                // Update status to Sent if still New
                                                if ($record->getRawOriginal('statuscode') === InvoiceStatus::New->value) {
                                                    $record->update(['statuscode' => InvoiceStatus::Sent->value]);
                                                }

                                                \Filament\Notifications\Notification::make()
                                                    ->title('Facture envoyée')
                                                    ->body("Email envoyé à {$member->email}")
                                                    ->success()
                                                    ->send();
                                            }),
                                        Components\Actions\Action::make('markPaid')
                                            ->label('Payé')
                                            ->icon('heroicon-o-banknotes')
                                            ->color('info')
                                            ->modalHeading('Marquer payée')
                                            ->modalSubmitActionLabel('OK')
                                            ->modalCancelActionLabel('Annuler')
                                            ->modalWidth('md')
                                            ->form(\App\Filament\Forms\PaymentDateForm::schema())
                                            ->action(function (array $data) {
                                                $record = $this->record;
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

                                                Member::assignMemberNumber($record->member);
                                                InvoicePaymentService::onCotisationPaid($record);

                                                Notification::make()
                                                    ->title('Facture payée')
                                                    ->body("Facture {$record->invoice_number} marquée payée le {$data['payment_date']}")
                                                    ->success()
                                                    ->send();

                                                $this->refreshFormData(['statuscode', 'payment_date', 'notes']);
                                            })
                                            ->visible(fn () => !in_array($this->record->getRawOriginal('statuscode'), [InvoiceStatus::Paid->value, InvoiceStatus::Cancelled->value])),
                                    ]),
                                ]),
                        ])->columnSpan(1),
                    ]),

                // Invoice lines
                Components\Section::make('Lignes')
                    ->schema([
                        Components\ViewEntry::make('lines')
                            ->label('')
                            ->view('filament.infolists.invoice-lines'),
                    ]),

                // Linked events (type E)
                Components\Section::make('Événements liés')
                    ->schema([
                        Components\ViewEntry::make('events')
                            ->label('')
                            ->view('filament.infolists.invoice-events'),
                    ])
                    ->hidden(fn ($record) => $record->getRawOriginal('type') !== InvoiceType::Evenement->value || $record->events->isEmpty()),

                // Last modified
                Components\Section::make()
                    ->schema([
                        Components\Grid::make(2)
                            ->schema([
                                Components\TextEntry::make('updated_at')
                                    ->label('Dernière modification')
                                    ->icon('heroicon-o-clock')
                                    ->dateTime('d.m.Y H:i'),
                                Components\TextEntry::make('modifiedBy.name')
                                    ->label('Par')
                                    ->icon('heroicon-o-user')
                                    ->placeholder('—'),
                            ]),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('Modifier')
                ->icon('heroicon-o-pencil-square')
                ->color('info'),
        ];
    }
}
