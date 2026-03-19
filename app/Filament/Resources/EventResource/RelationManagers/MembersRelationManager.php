<?php

namespace App\Filament\Resources\EventResource\RelationManagers;

use App\Enums\EventMemberStatus;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class MembersRelationManager extends RelationManager
{
    protected static string $relationship = 'members';

    protected static ?string $title = 'Participantes';

    protected static ?string $modelLabel = 'Participante';

    protected static ?string $pluralModelLabel = 'Participantes';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('status')
                    ->label('Statut')
                    ->options(collect(EventMemberStatus::cases())->mapWithKeys(fn ($s) => [$s->value => $s->getLabel()]))
                    ->default('N')
                    ->required(),
            ]);
    }

    protected function canAttach(): bool
    {
        return $this->canManageParticipants();
    }

    protected function canDetach(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return $this->canManageParticipants();
    }

    private function canManageParticipants(): bool
    {
        $user = auth()->user();

        return $user->isAdmin()
            || $user->member_id === $this->getOwnerRecord()->chef_peloton_id;
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('last_name')
            ->columns([
                Tables\Columns\TextColumn::make('last_name')
                    ->label('Nom')
                    ->sortable(),
                Tables\Columns\TextColumn::make('first_name')
                    ->label('Prénom')
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('E-mail'),
                Tables\Columns\TextColumn::make('pivot_status')
                    ->label('Statut')
                    ->badge()
                    ->state(fn ($record) => $record->pivot->getRawOriginal('status'))
                    ->formatStateUsing(fn (string $state) => EventMemberStatus::from($state)->getLabel())
                    ->color(fn (string $state) => EventMemberStatus::from($state)->getColor()),
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->label('Ajouter une participante')
                    ->preloadRecordSelect()
                    ->form(fn (Tables\Actions\AttachAction $action): array => [
                        $action->getRecordSelect()
                            ->label('Membre'),
                        Forms\Components\Select::make('status')
                            ->label('Statut')
                            ->options(collect(EventMemberStatus::cases())->mapWithKeys(fn ($s) => [$s->value => $s->getLabel()]))
                            ->default('N')
                            ->required(),
                    ])
                    ->visible(fn () => $this->canManageParticipants()),
            ])
            ->actions([
                Tables\Actions\Action::make('changeStatus')
                    ->label('Statut')
                    ->icon('heroicon-o-pencil-square')
                    ->form([
                        Forms\Components\Select::make('status')
                            ->label('Statut')
                            ->options(collect(EventMemberStatus::cases())->mapWithKeys(fn ($s) => [$s->value => $s->getLabel()]))
                            ->required(),
                    ])
                    ->fillForm(fn ($record) => ['status' => $record->pivot->getRawOriginal('status')])
                    ->action(fn ($record, array $data) => $record->pivot->update(['status' => $data['status']]))
                    ->visible(fn () => $this->canManageParticipants()),
                Tables\Actions\DetachAction::make()
                    ->label('Retirer')
                    ->visible(fn () => $this->canManageParticipants()),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make()
                        ->label('Retirer')
                        ->visible(fn () => $this->canManageParticipants()),
                ]),
            ]);
    }
}
