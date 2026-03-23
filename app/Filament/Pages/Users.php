<?php

namespace App\Filament\Pages;

use App\Enums\UserRole;
use App\Models\User;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;

class Users extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-key';

    protected static ?string $title = 'Utilisateurs';

    protected static ?string $navigationLabel = 'Utilisateurs';

    protected static ?string $slug = 'users';

    protected static string $view = 'filament.pages.users';

    protected static bool $shouldRegisterNavigation = false;

    public function mount(): void
    {
        abort_unless(auth()->user()->isAdmin(), 403);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(User::query())
            ->defaultSort('name', 'asc')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nom')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('role')
                    ->label('Rôle')
                    ->badge()
                    ->formatStateUsing(fn (UserRole $state) => $state->getLabel())
                    ->color(fn (UserRole $state) => match ($state) {
                        UserRole::Admin => 'danger',
                        UserRole::ChefPeloton => 'info',
                    }),
                Tables\Columns\IconColumn::make('is_locked')
                    ->label('Verrouillé')
                    ->boolean()
                    ->trueIcon('heroicon-o-lock-closed')
                    ->falseIcon('heroicon-o-lock-open')
                    ->trueColor('danger')
                    ->falseColor('success'),
                Tables\Columns\TextColumn::make('member.full_name')
                    ->label('Membre lié')
                    ->state(fn ($record) => $record->member
                        ? $record->member->first_name . ' ' . $record->member->last_name
                        : '—'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Modifié')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('edit')
                    ->label('Modifier')
                    ->icon('heroicon-o-pencil-square')
                    ->modalHeading(fn ($record) => 'Modifier ' . $record->name)
                    ->modalSubmitActionLabel('Enregistrer')
                    ->fillForm(fn ($record) => [
                        'name' => $record->name,
                        'email' => $record->email,
                        'role' => $record->getRawOriginal('role'),
                        'member_id' => $record->member_id,
                    ])
                    ->form([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nom')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('email')
                                    ->label('Email')
                                    ->email()
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\Select::make('role')
                                    ->label('Rôle')
                                    ->options(collect(UserRole::cases())->mapWithKeys(fn ($r) => [$r->value => $r->getLabel()]))
                                    ->required(),
                                Forms\Components\Select::make('member_id')
                                    ->label('Membre lié')
                                    ->relationship('member', 'last_name')
                                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->first_name . ' ' . $record->last_name)
                                    ->searchable(['first_name', 'last_name'])
                                    ->preload()
                                    ->nullable(),
                            ]),
                        Forms\Components\TextInput::make('password')
                            ->label('Nouveau mot de passe')
                            ->password()
                            ->helperText('Laisser vide pour ne pas changer')
                            ->minLength(8),
                    ])
                    ->action(function ($record, array $data) {
                        $updates = [
                            'name' => $data['name'],
                            'email' => $data['email'],
                            'role' => $data['role'],
                            'member_id' => $data['member_id'],
                        ];

                        if (!empty($data['password'])) {
                            $updates['password'] = $data['password'];
                        }

                        $record->update($updates);

                        Notification::make()
                            ->title('Utilisateur modifié')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('lock')
                    ->label('Verrouiller')
                    ->icon('heroicon-o-lock-closed')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Verrouiller cet utilisateur ?')
                    ->modalDescription('L\'utilisateur ne pourra plus se connecter et son adresse email sera supprimée (pas de récupération de mot de passe possible).')
                    ->hidden(fn ($record) => $record->id === auth()->id() || $record->is_locked)
                    ->action(function ($record) {
                        $record->update([
                            'is_locked' => true,
                            'email' => 'locked_' . $record->id . '@disabled.local',
                            'remember_token' => null,
                        ]);

                        Notification::make()
                            ->title('Utilisateur verrouillé')
                            ->body($record->name . ' ne peut plus se connecter.')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('unlock')
                    ->label('Déverrouiller')
                    ->icon('heroicon-o-lock-open')
                    ->color('success')
                    ->hidden(fn ($record) => !$record->is_locked)
                    ->form([
                        Forms\Components\TextInput::make('email')
                            ->label('Nouvelle adresse email')
                            ->email()
                            ->required()
                            ->unique('users', 'email'),
                    ])
                    ->action(function ($record, array $data) {
                        $record->update([
                            'is_locked' => false,
                            'email' => $data['email'],
                        ]);

                        Notification::make()
                            ->title('Utilisateur déverrouillé')
                            ->success()
                            ->send();
                    }),
            ])
            ->headerActions([
                Tables\Actions\Action::make('create')
                    ->label('Nouvel utilisateur')
                    ->icon('heroicon-o-plus')
                    ->form([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nom')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('email')
                                    ->label('Email')
                                    ->email()
                                    ->required()
                                    ->unique('users', 'email')
                                    ->maxLength(255),
                                Forms\Components\Select::make('role')
                                    ->label('Rôle')
                                    ->options(collect(UserRole::cases())->mapWithKeys(fn ($r) => [$r->value => $r->getLabel()]))
                                    ->default('C')
                                    ->required(),
                                Forms\Components\Select::make('member_id')
                                    ->label('Membre lié')
                                    ->options(fn () => \App\Models\Member::whereNull('deleted_at')
                                        ->orderBy('last_name')
                                        ->get()
                                        ->mapWithKeys(fn ($m) => [$m->id => $m->first_name . ' ' . $m->last_name]))
                                    ->searchable()
                                    ->nullable(),
                            ]),
                        Forms\Components\TextInput::make('password')
                            ->label('Mot de passe')
                            ->password()
                            ->required()
                            ->minLength(8),
                    ])
                    ->action(function (array $data) {
                        User::create($data);

                        Notification::make()
                            ->title('Utilisateur créé')
                            ->success()
                            ->send();
                    }),
            ]);
    }
}
