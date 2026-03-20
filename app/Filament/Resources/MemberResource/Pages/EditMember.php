<?php

namespace App\Filament\Resources\MemberResource\Pages;

use App\Filament\Resources\MemberResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMember extends EditRecord
{
    protected static string $resource = MemberResource::class;

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
        ];
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction(),
            $this->getCancelFormAction(),
            Actions\Action::make('delete')
                ->label('Supprimer')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->delete();
                    $this->redirect(MemberResource::getUrl('index'));
                }),
        ];
    }
}
