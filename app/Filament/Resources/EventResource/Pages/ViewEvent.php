<?php

namespace App\Filament\Resources\EventResource\Pages;

use App\Filament\Resources\EventResource;
use App\Filament\Resources\EventResource\RelationManagers;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewEvent extends ViewRecord
{
    protected static string $resource = EventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()->label('Modifier'),
        ];
    }

    public function getRelationManagers(): array
    {
        return [
            RelationManagers\MembersRelationManager::class,
            RelationManagers\PresencesRelationManager::class,
        ];
    }
}
