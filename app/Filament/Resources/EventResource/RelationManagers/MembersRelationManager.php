<?php

namespace App\Filament\Resources\EventResource\RelationManagers;

use App\Enums\EventMemberStatus;
use App\Enums\InvoiceType;
use App\Enums\MemberStatus;
use App\Mail\EventConfirmationMail;
use App\Models\Invoice;
use App\Models\Member;
use App\Services\ExcelExportService;
use App\Services\InvoiceEmailService;
use Filament\Forms;
use Filament\Notifications\Notification;
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
                    ->default(EventMemberStatus::Inscrit->value)
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
            || ($user->member_id && $this->getOwnerRecord()->chefs->contains('id', $user->member_id));
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('first_name')
            ->columns([
                Tables\Columns\IconColumn::make('statuscode')
                    ->label('')
                    ->icon(fn ($record) => match ($record->getRawOriginal('statuscode')) {
                        'N' => 'heroicon-o-minus',
                        default => 'heroicon-s-user',
                    })
                    ->color(fn ($record) => match ($record->getRawOriginal('statuscode')) {
                        'A', 'E' => 'primary',
                        'N' => 'info',
                        default => 'gray',
                    })
                    ->tooltip(fn ($record) => $record->statuscode->getLabel())
                    ->grow(false)
                    ->size(Tables\Columns\IconColumn\IconColumnSize::Small),
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
            ->modifyQueryUsing(fn ($query) => $query->whereNull('event_member.deleted_at'))
            ->filters([
                Tables\Filters\TernaryFilter::make('actives')
                    ->label('Inscrites/Confirmées')
                    ->default(true)
                    ->queries(
                        true: fn ($query) => $query->whereIn('event_member.status', [EventMemberStatus::Inscrit->value, EventMemberStatus::Confirme->value]),
                        false: fn ($query) => $query->where('event_member.status', EventMemberStatus::Annule->value),
                        blank: fn ($query) => $query,
                    ),
            ])
            ->headerActions([
                Tables\Actions\Action::make('exportExcel')
                    ->label('')
                    ->tooltip('Exporter la liste')
                    ->icon(fn () => new \Illuminate\Support\HtmlString('<img src="' . asset('images/ms-excel.svg') . '" style="width:1.25rem;height:1.25rem;">'))
                    ->color('gray')
                    ->action(function () {
                        $result = ExcelExportService::exportParticipants($this->getOwnerRecord());

                        return response()->download($result['path'], $result['filename'], [
                            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        ])->deleteFileAfterSend();
                    }),
                Tables\Actions\AttachAction::make()
                    ->label('Ajouter')
                    ->icon('heroicon-o-plus')
                    ->color('primary')
                    ->recordSelectSearchColumns(['first_name', 'last_name', 'member_number'])
                    ->recordSelectOptionsQuery(fn ($query) => $query->whereIn('statuscode', [MemberStatus::Actif->value, MemberStatus::EnAttente->value, MemberStatus::Inactif->value]))
                    ->preloadRecordSelect()
                    ->recordTitle(fn (Member $record) =>
                        $record->first_name . ' ' . $record->last_name .
                        ($record->member_number ? ' (#' . $record->member_number . ')' : '')
                    )
                    ->form(fn (Tables\Actions\AttachAction $action): array => [
                        $action->getRecordSelect()
                            ->label('Membre'),
                        Forms\Components\Select::make('status')
                            ->label('Statut')
                            ->options(collect(EventMemberStatus::cases())->mapWithKeys(fn ($s) => [$s->value => $s->getLabel()]))
                            ->default(EventMemberStatus::Inscrit->value)
                            ->required(),
                    ])
                    ->after(function ($record) {
                        $event = $this->getOwnerRecord();
                        $member = $record;
                        $applicablePrice = (float) $event->priceForMember($member);

                        if ($applicablePrice > 0) {
                            InvoiceEmailService::createAndSendEvent($member, $event);
                        }
                    })
                    ->visible(fn () => $this->canManageParticipants()),
            ])
            ->actions([
                Tables\Actions\Action::make('resendEmail')
                    ->label('')
                    ->tooltip('Renvoyer l\'email d\'inscription')
                    ->icon('heroicon-o-envelope')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->modalHeading('Renvoyer l\'email')
                    ->modalDescription(fn ($record) => 'Renvoyer l\'email d\'inscription à ' . $record->first_name . ' ' . $record->last_name . ' ?')
                    ->action(function ($record) {
                        $event = $this->getOwnerRecord();
                        $member = $record;
                        $applicablePrice = (float) $event->priceForMember($member);

                        if ($applicablePrice > 0) {
                            $invoice = Invoice::where('member_id', $member->id)
                                ->where('type', InvoiceType::Evenement->value)
                                ->whereHas('events', fn ($q) => $q->where('events.id', $event->id))
                                ->latest('updated_at')
                                ->first();

                            if ($invoice) {
                                InvoiceEmailService::sendExisting($invoice, $event);
                            }
                        } else {
                            Mail::send(new EventConfirmationMail($member, $event));
                        }

                        Notification::make()
                            ->success()
                            ->title('Email envoyé')
                            ->body('L\'email a été renvoyé à ' . $member->first_name . ' ' . $member->last_name)
                            ->send();
                    })
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
