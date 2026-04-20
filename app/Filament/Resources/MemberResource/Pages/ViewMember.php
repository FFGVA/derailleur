<?php

namespace App\Filament\Resources\MemberResource\Pages;

use App\Enums\MemberStatus;
use App\Filament\Resources\MemberResource;
use App\Mail\AdhesionWelcomeMail;
use App\Services\MemberCardService;
use Filament\Actions;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

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
                            Components\Section::make(new \Illuminate\Support\HtmlString('<span style="display:inline-flex;align-items:center;gap:0.5rem;"><img src="' . asset('images/contact.svg') . '" style="width:1.25rem;height:1.25rem;filter:invert(50%);"> Contact</span>'))
                                ->schema([
                                    Components\TextEntry::make('email')
                                        ->label('')
                                        ->icon('heroicon-o-envelope')
                                        ->url(fn ($record) => 'mailto:' . $record->email)
                                        ->color('primary'),
                                    Components\ViewEntry::make('phones')
                                        ->label('Téléphones')
                                        ->view('filament.infolists.phones'),
                                ]),
                            Components\Section::make('Réseaux sociaux')
                                ->icon('heroicon-o-globe-alt')
                                ->columns(2)
                                ->schema([
                                    Components\ViewEntry::make('instagram_link')
                                        ->label('')
                                        ->view('filament.infolists.social-link')
                                        ->viewData([
                                            'icon' => asset('images/instagram-logo.svg'),
                                            'getUrl' => fn ($record) => ($record->metadata['instagram'] ?? false) ? 'https://instagram.com/' . ltrim($record->metadata['instagram'], '@') : null,
                                            'getText' => fn ($record) => ($record->metadata['instagram'] ?? false) ? '@' . ltrim($record->metadata['instagram'], '@') : null,
                                        ]),
                                    Components\ViewEntry::make('strava_link')
                                        ->label('')
                                        ->view('filament.infolists.social-link')
                                        ->viewData([
                                            'icon' => asset('images/strava-logo.svg'),
                                            'getUrl' => fn ($record) => ($record->metadata['strava'] ?? false) ? 'https://www.strava.com/athletes/' . urlencode($record->metadata['strava']) : null,
                                            'getText' => fn ($record) => $record->metadata['strava'] ?? null,
                                        ]),
                                ])
                                ->hidden(fn ($record) => empty($record->metadata['instagram']) && empty($record->metadata['strava'])),
                            Components\Section::make('Activités')
                                ->schema([
                                    Components\ViewEntry::make('events')
                                        ->label('')
                                        ->view('filament.infolists.member-events'),
                                ])
                                ->hidden(fn ($record) => $record->events->isEmpty() && $record->ledEvents->isEmpty()),
                            Components\Section::make('Factures')
                                ->icon('heroicon-o-document-text')
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
                                        ->color(fn (MemberStatus $state) => $state->getColor())
                                        ->hintAction(
                                            Components\Actions\Action::make('resendActivation')
                                                ->icon('heroicon-o-envelope')
                                                ->tooltip('Renvoyer la confirmation de l\'email')
                                                ->color('gray')
                                                ->size('sm')
                                                ->requiresConfirmation()
                                                ->modalHeading('Renvoyer l\'email de confirmation')
                                                ->modalDescription(fn () => 'Renvoyer l\'email d\'activation à ' . $this->record->first_name . ' ' . $this->record->last_name . ' (' . $this->record->email . ') ?')
                                                ->action(function () {
                                                    $member = $this->record;
                                                    $rawToken = bin2hex(random_bytes(32));
                                                    $member->update([
                                                        'activation_token' => Hash::make($rawToken),
                                                        'activation_sent_at' => now(),
                                                    ]);

                                                    $activationUrl = url("/adhesion/confirmer?token={$rawToken}&email={$member->email}");
                                                    Mail::send(new AdhesionWelcomeMail($member, $activationUrl));

                                                    Notification::make()
                                                        ->success()
                                                        ->title('Email envoyé')
                                                        ->body('Email de confirmation renvoyé à ' . $member->email)
                                                        ->send();
                                                })
                                                ->visible(fn () => $this->record->getRawOriginal('statuscode') === 'P' && $this->record->activation_token)
                                        ),
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

                Components\Section::make('Métadonnées')
                    ->icon('heroicon-o-table-cells')
                    ->schema([
                        Components\ViewEntry::make('metadata_display')
                            ->label('')
                            ->view('filament.infolists.member-metadata'),
                    ])
                    ->collapsed()
                    ->hidden(fn ($record) => empty($record->metadata)),

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
            Actions\Action::make('downloadCard')
                ->label('Carte')
                ->icon(fn () => new \Illuminate\Support\HtmlString('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" style="width:1.25rem;height:1.25rem;"><path d="M5 4h14a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2zm7.5 7V7h-1v4H8.25l3.25 3.25L14.75 11H11.5zM7 16h10v1H7v-1z"/></svg>'))
                ->color('gray')
                ->visible(fn () => in_array($this->record->getRawOriginal('statuscode'), ['A', 'E']))
                ->action(function () {
                    $member = $this->record;
                    $pdf = MemberCardService::generate($member);
                    $filename = MemberCardService::filename($member);

                    return response()->streamDownload(
                        function () use ($pdf) { echo $pdf; },
                        $filename,
                        ['Content-Type' => 'application/pdf']
                    );
                }),
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
