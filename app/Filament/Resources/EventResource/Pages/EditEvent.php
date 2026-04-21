<?php

namespace App\Filament\Resources\EventResource\Pages;

use App\Filament\Resources\EventResource;
use App\Models\EventChef;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditEvent extends EditRecord
{
    protected static string $resource = EventResource::class;

    public function getTitle(): string
    {
        return $this->record->title;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->before(function (Actions\DeleteAction $action) {
                    if ($this->record->members()->count() > 0) {
                        Notification::make()
                            ->title('Suppression impossible')
                            ->body('Cet événement a des participantes. Retirez-les d\'abord.')
                            ->danger()
                            ->send();
                        $action->halt();
                    }
                })
                ->visible(fn () => auth()->user()->isAdmin()),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Load current chef IDs from pivot table
        $data['chef_ids'] = $this->record->chefs->pluck('id')->toArray();

        return $data;
    }

    protected function afterSave(): void
    {
        $newChefIds = collect($this->data['chef_ids'] ?? []);
        $currentChefIds = $this->record->chefs->pluck('id');

        // Soft-delete removed chefs
        $toRemove = $currentChefIds->diff($newChefIds);
        if ($toRemove->isNotEmpty()) {
            EventChef::where('event_id', $this->record->id)
                ->whereIn('member_id', $toRemove)
                ->whereNull('deleted_at')
                ->each(fn ($ec) => $ec->delete());
        }

        // Add new chefs (restore soft-deleted or create)
        foreach ($newChefIds->values() as $i => $memberId) {
            $existing = EventChef::withTrashed()
                ->where('event_id', $this->record->id)
                ->where('member_id', $memberId)
                ->first();

            if ($existing && $existing->trashed()) {
                $existing->restore();
                $existing->update(['sort_order' => $i]);
            } elseif (!$existing) {
                EventChef::create([
                    'event_id' => $this->record->id,
                    'member_id' => $memberId,
                    'sort_order' => $i,
                ]);
            } else {
                $existing->update(['sort_order' => $i]);
            }
        }
    }
}
