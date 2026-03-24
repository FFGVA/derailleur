<?php

namespace App\Filament\Resources\EventResource\Pages;

use App\Filament\Resources\EventResource;
use App\Models\EventChef;
use Filament\Resources\Pages\CreateRecord;

class CreateEvent extends CreateRecord
{
    protected static string $resource = EventResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterCreate(): void
    {
        $chefIds = $this->data['chef_ids'] ?? [];

        foreach ($chefIds as $i => $memberId) {
            EventChef::create([
                'event_id' => $this->record->id,
                'member_id' => $memberId,
                'sort_order' => $i,
            ]);
        }
    }
}
