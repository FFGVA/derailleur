<?php

namespace App\Filament\Resources\MemberResource\Pages;

use App\Filament\Resources\MemberResource;
use App\Models\Member;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMembers extends ListRecords
{
    protected static string $resource = MemberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('downloadVcards')
                ->label('vCards')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->action(function () {
                    $members = Member::with('phones')->get();
                    $vcf = $members->map(function ($member) {
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
            Actions\CreateAction::make()->label('Nouveau membre'),
        ];
    }
}
