<x-filament-panels::page>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        {{-- Stats cards --}}
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
                <div class="text-3xl font-bold text-primary-600 dark:text-primary-400">
                    {{ $this->getLinkedMembersCount() }}
                </div>
                <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Comptes Strava liés
                </div>
            </div>
        </x-filament::section>

        <x-filament::section>
            <div class="text-center">
                <div class="text-3xl font-bold text-gray-400 dark:text-gray-500">
                    —
                </div>
                <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Configuration en attente
                </div>
            </div>
        </x-filament::section>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Linked events --}}
        <x-filament::section heading="Événements liés à Strava" icon="heroicon-o-calendar-days">
            @php $events = $this->getLinkedEvents(); @endphp
            @if($events->isEmpty())
                <div class="text-sm text-gray-500 dark:text-gray-400 py-4 text-center">
                    Aucun événement lié à Strava pour le moment.
                    <br>
                    <span class="text-xs">Liez un événement en saisissant l'ID Strava dans le formulaire d'édition de l'événement.</span>
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

        {{-- Linked members --}}
        <x-filament::section heading="Comptes Strava liés" icon="heroicon-o-users">
            @php $members = $this->getLinkedMembers(); @endphp
            @if($members->isEmpty())
                <div class="text-sm text-gray-500 dark:text-gray-400 py-4 text-center">
                    Aucun compte Strava lié pour le moment.
                    <br>
                    <span class="text-xs">La connexion Strava sera disponible dans le profil membre.</span>
                </div>
            @else
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($members as $link)
                        <div class="py-3 flex items-center justify-between">
                            <div>
                                <a href="{{ \App\Filament\Resources\MemberResource::getUrl('view', ['record' => $link->member]) }}"
                                   class="text-sm font-medium text-primary-600 dark:text-primary-400 hover:underline">
                                    {{ $link->member->first_name }} {{ $link->member->last_name }}
                                </a>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    Athlète #{{ $link->strava_athlete_id }}
                                    · Token expire {{ $link->token_expires_at->format('d.m.Y H:i') }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </x-filament::section>
    </div>
</x-filament-panels::page>
