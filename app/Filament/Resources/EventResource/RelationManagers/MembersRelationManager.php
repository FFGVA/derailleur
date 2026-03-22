<?php

namespace App\Filament\Resources\EventResource\RelationManagers;

use App\Enums\EventMemberStatus;
use App\Mail\InvoiceMail;
use App\Models\Invoice;
use App\Models\Member;
use App\Services\ICalService;
use App\Services\InvoiceService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Mail;

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
                Tables\Columns\TextColumn::make('pivot_present')
                    ->label('Présence')
                    ->state(fn ($record) => match ($record->pivot->present) {
                        true => '✓',
                        false => '✗',
                        default => '—',
                    })
                    ->color(fn ($record) => match ($record->pivot->present) {
                        true => 'success',
                        false => 'danger',
                        default => 'gray',
                    })
                    ->badge()
                    ->alignCenter()
                    ->action(
                        Tables\Actions\Action::make('togglePresence')
                            ->action(function ($record) {
                                $current = $record->pivot->present;
                                $next = match ($current) {
                                    null => true,
                                    true => false,
                                    false => null,
                                };
                                $record->pivot->update(['present' => $next]);
                            })
                            ->visible(fn () => $this->canManageParticipants())
                    ),
                Tables\Columns\TextColumn::make('email')
                    ->label('E-mail'),
                Tables\Columns\TextColumn::make('pivot_status')
                    ->label('Statut')
                    ->badge()
                    ->state(fn ($record) => $record->pivot->getRawOriginal('status'))
                    ->formatStateUsing(fn (string $state) => EventMemberStatus::from($state)->getLabel())
                    ->color(fn (string $state) => EventMemberStatus::from($state)->getColor())
                    ->action(
                        Tables\Actions\Action::make('changeStatus')
                            ->form([
                                Forms\Components\Select::make('status')
                                    ->label('Statut')
                                    ->options(collect(EventMemberStatus::cases())->mapWithKeys(fn ($s) => [$s->value => $s->getLabel()]))
                                    ->required(),
                            ])
                            ->fillForm(fn ($record) => ['status' => $record->pivot->getRawOriginal('status')])
                            ->action(fn ($record, array $data) => $record->pivot->update(['status' => $data['status']]))
                            ->visible(fn () => $this->canManageParticipants())
                    ),
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->label('Ajouter')
                    ->icon('heroicon-o-plus')
                    ->color('primary')
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
                    ->after(function ($record) {
                        $event = $this->getOwnerRecord();
                        $member = $record;
                        $applicablePrice = (float) $event->priceForMember($member);

                        if ($applicablePrice > 0) {
                            $result = InvoiceService::createEvent($member, $event);
                            $invoice = Invoice::where('invoice_number', $result['invoice_number'])->first();
                            $invoice->update(['statuscode' => 'E']);

                            $qrBase64 = InvoiceService::generateQrCodeBase64($invoice);
                            $ical = ICalService::generate($event);
                            $icalFilename = ICalService::filename($event);
                            Mail::send(new InvoiceMail($invoice, $result['pdf'], $result['filename'], $qrBase64, $ical, $icalFilename));
                        }
                    })
                    ->visible(fn () => $this->canManageParticipants()),
            ])
            ->actions([
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
