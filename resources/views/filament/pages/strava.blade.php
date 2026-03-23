<x-filament-panels::page>
    {{-- Configuration check --}}
    @unless($this->isConfigured())
        <x-filament::section icon="heroicon-o-exclamation-triangle" icon-color="warning">
            <x-slot name="heading">Configuration requise</x-slot>
            <p class="text-sm text-gray-600 dark:text-gray-400">
                Les variables <code>STRAVA_CLIENT_ID</code> et <code>STRAVA_CLIENT_SECRET</code> doivent être définies dans le fichier <code>.env</code>.
            </p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                Créer une application sur <a href="https://www.strava.com/settings/api" target="_blank" class="text-primary-600 hover:underline">strava.com/settings/api</a>
                avec l'URL de callback : <code>{{ route('strava.callback') }}</code>
            </p>
        </x-filament::section>
    @endunless

    {{-- Stats cards --}}
    @php $accounts = $this->getStravaAccounts(); @endphp
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <x-filament::section>
            <div class="text-center">
                <div class="text-3xl font-bold text-primary-600 dark:text-primary-400">
                    {{ $accounts->count() }}
                </div>
                <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Comptes Strava liés
                </div>
            </div>
        </x-filament::section>

        <x-filament::section>
            <div class="text-center">
                <div class="text-3xl font-bold text-primary-600 dark:text-primary-400">
                    {{ $this->getLinkedEventsCount() }}
                </div>
                <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Événements liés
                </div>
            </div>
        </x-filament::section>

        <x-filament::section>
            <div class="text-center">
                <div class="text-3xl font-bold {{ config('ffgva.strava_club_id') ? 'text-primary-600 dark:text-primary-400' : 'text-gray-400 dark:text-gray-500' }}">
                    {{ config('ffgva.strava_club_id') ?: '—' }}
                </div>
                <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Club ID Strava
                </div>
            </div>
        </x-filament::section>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Connected Strava accounts --}}
        <x-filament::section heading="Comptes Strava" icon="heroicon-o-users">
            @if($accounts->isEmpty())
                <div class="text-sm text-gray-500 dark:text-gray-400 py-4 text-center">
                    Aucun compte Strava connecté.
                </div>
            @else
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($accounts as $link)
                        <div class="py-3 flex items-center justify-between">
                            <div>
                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                    Athlète #{{ $link->strava_athlete_id }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    @if($link->user)
                                        Utilisateur : {{ $link->user->name }}
                                    @endif
                                    @if($link->member)
                                        · Membre :
                                        <a href="{{ \App\Filament\Resources\MemberResource::getUrl('view', ['record' => $link->member]) }}"
                                           class="text-primary-600 dark:text-primary-400 hover:underline">
                                            {{ $link->member->first_name }} {{ $link->member->last_name }}
                                        </a>
                                    @endif
                                </div>
                                <div class="text-xs text-gray-400 dark:text-gray-500">
                                    Token expire {{ $link->token_expires_at->format('d.m.Y H:i') }}
                                    · Scopes : {{ $link->scopes }}
                                </div>
                            </div>
                            <form action="{{ route('strava.disconnect') }}" method="POST"
                                  onsubmit="return confirm('Déconnecter ce compte Strava ?')">
                                @csrf
                                <input type="hidden" name="strava_id" value="{{ $link->id }}">
                                <x-filament::button size="xs" color="danger" type="submit" icon="heroicon-o-x-mark">
                                    Déconnecter
                                </x-filament::button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Connect new account --}}
            @if($this->isConfigured())
                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <form action="{{ route('strava.redirect') }}" method="GET" class="flex items-end gap-3">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Connecter un compte Strava
                            </label>
                            <select name="member_id"
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm">
                                <option value="">Sans lien membre</option>
                                @foreach($this->getMembers() as $member)
                                    <option value="{{ $member->id }}">{{ $member->first_name }} {{ $member->last_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <x-filament::button type="submit" icon="heroicon-o-link" style="background-color: #FC4C02; border-color: #FC4C02;">
                            Connecter Strava
                        </x-filament::button>
                    </form>
                </div>
            @endif
        </x-filament::section>

        {{-- Linked events --}}
        <x-filament::section heading="Événements liés à Strava" icon="heroicon-o-calendar-days">
            @php $events = $this->getLinkedEvents(); @endphp
            @if($events->isEmpty())
                <div class="text-sm text-gray-500 dark:text-gray-400 py-4 text-center">
                    Aucun événement lié à Strava pour le moment.
                    <br>
                    <span class="text-xs">Liez un événement en saisissant l'ID Strava dans le formulaire d'édition.</span>
                </div>
            @else
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($events as $event)
                        <div class="py-3 flex items-center justify-between">
                            <div>
                                <a href="{{ \App\Filament\Resources\EventResource::getUrl('view', ['record' => $event]) }}"
                                   class="text-sm font-medium text-primary-600 dark:text-primary-400 hover:underline">
                                    {{ $event->title }}
                                </a>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $event->starts_at->format('d.m.Y H:i') }}
                                    @if($event->strava_route_id)
                                        · Parcours lié
                                    @endif
                                </div>
                            </div>
                            <div class="text-xs text-gray-400 dark:text-gray-500 font-mono">
                                #{{ $event->strava_event_id }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </x-filament::section>
    </div>

    {{-- Configuration info --}}
    <div class="mt-6">
        <x-filament::section heading="Configuration" icon="heroicon-o-cog-6-tooth" collapsible collapsed>
            <div class="text-sm text-gray-600 dark:text-gray-400 space-y-2">
                <div>
                    <span class="font-medium">Callback URL :</span>
                    <code class="text-xs bg-gray-100 dark:bg-gray-800 px-2 py-0.5 rounded">{{ route('strava.callback') }}</code>
                </div>
                <div>
                    <span class="font-medium">Client ID :</span>
                    <code class="text-xs bg-gray-100 dark:bg-gray-800 px-2 py-0.5 rounded">{{ config('ffgva.strava_client_id') ?: 'non configuré' }}</code>
                </div>
                <div>
                    <span class="font-medium">Club ID :</span>
                    <code class="text-xs bg-gray-100 dark:bg-gray-800 px-2 py-0.5 rounded">{{ config('ffgva.strava_club_id') ?: 'non configuré' }}</code>
                </div>
                <div>
                    <span class="font-medium">Scopes :</span>
                    <code class="text-xs bg-gray-100 dark:bg-gray-800 px-2 py-0.5 rounded">read, profile:read_all</code>
                </div>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
