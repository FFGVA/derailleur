<?php

namespace App\Filament\Resources\MemberResource\Pages;

use App\Filament\Resources\MemberResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMember extends EditRecord
{
    protected static string $resource = MemberResource::class;

    public function getTitle(): string
    {
        return $this->record->first_name . ' ' . $this->record->last_name;
    }

    protected function getHeaderActions(): array
    {
        return [];
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
                    $deps = [];
                    if ($this->record->events()->count() > 0) $deps[] = 'événements';
                    if ($this->record->invoices()->count() > 0) $deps[] = 'factures';
                    if ($this->record->ledEvents()->count() > 0) $deps[] = 'événements (cheffe de peloton)';

                    if (!empty($deps)) {
                        \Filament\Notifications\Notification::make()
                            ->title('Suppression impossible')
                            ->body('Ce membre a des ' . implode(', ', $deps) . '. Retirez-les d\'abord.')
                            ->danger()
                            ->send();
                        return;
                    }

                    // Soft-delete phones first
                    $this->record->phones()->delete();
                    $this->record->delete();
                    $this->redirect(MemberResource::getUrl('index'));
                }),
        ];
    }
}
