<?php

namespace App\Filament\Resources\MemberResource\Pages;

use App\Enums\MemberStatus;
use App\Filament\Resources\MemberResource;
use Filament\Actions;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components;
use Filament\Resources\Pages\ViewRecord;

class ViewMember extends ViewRecord
{
    protected static string $resource = MemberResource::class;

    public function getRelationManagers(): array
    {
        return [];
    }

    public function getTitle(): string
    {
        return $this->record->first_name . ' ' . $this->record->last_name;
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Components\Grid::make(3)
                    ->schema([
                        Components\Group::make([
                            Components\Section::make()
                                ->schema([
                                    Components\TextEntry::make('email')
                                        ->label('E-mail')
                                        ->icon('heroicon-o-envelope')
                                        ->url(fn ($record) => 'mailto:' . $record->email)
                                        ->color('primary'),
                                    Components\ViewEntry::make('phones')
                                        ->label('Téléphones')
                                        ->view('filament.infolists.phones'),
                                ]),
                            Components\Section::make('Activités')
                                ->schema([
                                    Components\ViewEntry::make('events')
                                        ->label('')
                                        ->view('filament.infolists.member-events'),
                                ])
                                ->hidden(fn ($record) => $record->events->isEmpty() && $record->ledEvents->isEmpty()),
                            Components\Section::make('Factures')
                                ->schema([
                                    Components\ViewEntry::make('member_invoices')
                                        ->label('')
                                        ->view('filament.infolists.member-invoices'),
                                ])
                                ->hidden(fn ($record) => $record->invoices()->whereNull('deleted_at')->count() === 0),
                        ])->columnSpan(2),

                        Components\Group::make([
                            Components\Section::make()
                                ->columns(2)
                                ->schema([
                                    Components\TextEntry::make('statuscode')
                                        ->label('Statut')
                                        ->badge()
                                        ->formatStateUsing(fn (MemberStatus $state) => $state->getLabel())
                                        ->color(fn (MemberStatus $state) => $state->getColor()),
                                    Components\TextEntry::make('member_number')
                                        ->label('N° membre')
                                        ->placeholder('—')
                                        ->weight('bold'),
                                    Components\TextEntry::make('membership_start')
                                        ->label('Début')
                                        ->date('d.m.Y')
                                        ->placeholder('—'),
                                    Components\TextEntry::make('membership_end')
                                        ->label('Fin')
                                        ->date('d.m.Y')
                                        ->placeholder('—'),
                                ]),
                            Components\Section::make()
                                ->schema([
                                    Components\TextEntry::make('full_address')
                                        ->label('Adresse')
                                        ->icon('heroicon-o-map-pin')
                                        ->state(function ($record) {
                                            $parts = [];
                                            if ($record->address) $parts[] = $record->address;
                                            $line2 = trim(($record->postal_code ?? '') . ' ' . ($record->city ?? ''));
                                            if ($line2) $parts[] = $line2;
                                            if ($record->country && $record->country !== 'CH') $parts[] = $record->country;
                                            return implode(', ', $parts) ?: '—';
                                        })
                                        ->url(function ($record) {
                                            $parts = [];
                                            if ($record->address) $parts[] = $record->address;
                                            if ($record->postal_code) $parts[] = $record->postal_code;
                                            if ($record->city) $parts[] = $record->city;
                                            if ($record->country) $parts[] = $record->country;
                                            return $parts ? 'https://maps.google.com/?q=' . urlencode(implode(' ', $parts)) : null;
                                        })
                                        ->openUrlInNewTab()
                                        ->color('primary'),
                                    Components\TextEntry::make('date_of_birth')
                                        ->label('Date de naissance')
                                        ->icon('heroicon-o-cake')
                                        ->date('d.m.Y')
                                        ->placeholder('—'),
                                    Components\TextEntry::make('metadata.instagram')
                                        ->label('Instagram')
                                        ->icon('heroicon-o-at-symbol')
                                        ->url(fn ($record) => $record->metadata['instagram'] ?? false ? 'https://instagram.com/' . ltrim($record->metadata['instagram'], '@') : null)
                                        ->openUrlInNewTab()
                                        ->color('primary')
                                        ->placeholder('—'),
                                ]),
                        ])->columnSpan(1),
                    ]),

                Components\Section::make()
                    ->schema([
                        Components\TextEntry::make('notes')
                            ->label('Notes')
                            ->placeholder('—')
                            ->markdown()
                            ->columnSpanFull(),
                    ])
                    ->hidden(fn ($record) => empty($record->notes)),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('downloadVcard')
                ->label('vCard')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->action(function () {
                    $member = $this->record->load('phones');
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
                    $vcf = implode("\r\n", $lines);

                    return response()->streamDownload(
                        function () use ($vcf) { echo $vcf; },
                        $member->first_name . '-' . $member->last_name . '.vcf',
                        ['Content-Type' => 'text/vcard']
                    );
                }),
            Actions\EditAction::make()
                ->label('Modifier')
                ->icon('heroicon-o-pencil-square')
                ->color('info'),
        ];
    }
}
