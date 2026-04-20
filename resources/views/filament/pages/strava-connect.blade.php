<x-filament-panels::page>
    @php
        $connection = $this->getConnection();
        $configured = $this->isConfigured();
        $apiStatus = $connection ? $this->testConnection() : null;
    @endphp

    {{-- Status banner --}}
    @if($connection && $apiStatus)
        <x-filament::section>
            <div class="flex items-center gap-4">
                @if($apiStatus['ok'])
                    <div class="flex-shrink-0 w-4 h-4 rounded-full bg-green-500"></div>
                    <div>
                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                            API Strava connectée
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $apiStatus['athlete']['name'] }}
                            (athlète #{{ $apiStatus['athlete']['id'] }})
                            @if($apiStatus['athlete']['city'])
                                · {{ $apiStatus['athlete']['city'] }}
                            @endif
                        </div>
                    </div>
                @else
                    <div class="flex-shrink-0 w-4 h-4 rounded-full bg-red-500"></div>
                    <div>
                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                            Connexion Strava en erreur
                        </div>
                        <div class="text-xs text-red-600 dark:text-red-400">
                            {{ $apiStatus['error'] }}
                        </div>
                    </div>
                @endif
            </div>
        </x-filament::section>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Connection card --}}
        <x-filament::section heading="Connexion Strava" icon="heroicon-o-key">
            @if(!$configured)
                <div class="py-4 space-y-3">
                    <div class="flex items-center gap-2 text-sm text-amber-600 dark:text-amber-400">
                        <x-heroicon-o-exclamation-triangle class="w-5 h-5 flex-shrink-0" />
                        <span class="font-medium">Configuration requise</span>
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        <code>STRAVA_CLIENT_ID</code> et <code>STRAVA_CLIENT_SECRET</code> doivent être définis dans <code>.env</code>.
                    </p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Enregistrer l'application sur
                        <a href="https://www.strava.com/settings/api" target="_blank" class="text-primary-600 hover:underline">strava.com/settings/api</a>
                        avec le callback :
                    </p>
                    <code class="block text-xs bg-gray-100 dark:bg-gray-800 px-3 py-2 rounded">{{ route('strava.callback') }}</code>
                </div>
            @elseif(!$connection)
                <div class="py-4 space-y-4 text-center">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Aucun compte Strava connecté.
                    </p>
                    <a href="{{ route('strava.redirect') }}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-white text-sm font-medium"
                       style="background-color: #FC4C02;">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M15.387 17.944l-2.089-4.116h-3.065L15.387 24l5.15-10.172h-3.066m-7.008-5.599l2.836 5.598h4.172L10.463 0l-7 13.828h4.169"/>
                        </svg>
                        Connecter Strava
                    </a>
                </div>
            @else
                <div class="py-2 space-y-4">
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        <div class="py-2 flex justify-between text-sm">
                            <span class="text-gray-500 dark:text-gray-400">Athlète ID</span>
                            <span class="font-mono text-gray-900 dark:text-gray-100">{{ $connection->strava_athlete_id }}</span>
                        </div>
                        <div class="py-2 flex justify-between text-sm">
                            <span class="text-gray-500 dark:text-gray-400">Scopes</span>
                            <span class="font-mono text-gray-900 dark:text-gray-100">{{ $connection->scopes }}</span>
                        </div>
                        <div class="py-2 flex justify-between text-sm">
                            <span class="text-gray-500 dark:text-gray-400">Token expire</span>
                            <span class="text-gray-900 dark:text-gray-100">
                                {{ $connection->token_expires_at->format('d.m.Y H:i') }}
                                @if($connection->token_expires_at->isPast())
                                    <span class="text-xs text-amber-500">(rafraîchi automatiquement)</span>
                                @endif
                            </span>
                        </div>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <a href="{{ route('strava.redirect') }}"
                           class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-white text-xs font-medium"
                           style="background-color: #FC4C02;">
                            Reconnecter
                        </a>
                        <form action="{{ route('strava.disconnect') }}" method="POST"
                              onsubmit="return confirm('Déconnecter le compte Strava ?')">
                            @csrf
                            <input type="hidden" name="strava_id" value="{{ $connection->id }}">
                            <x-filament::button size="xs" color="danger" type="submit">
                                Déconnecter
                            </x-filament::button>
                        </form>
                    </div>
                </div>
            @endif
        </x-filament::section>

        {{-- Info card --}}
        <x-filament::section heading="Informations" icon="heroicon-o-information-circle">
            <div class="text-sm text-gray-600 dark:text-gray-400 space-y-3">
                <p>Le compte connecté doit être <strong>admin du club Strava</strong> pour accéder aux événements et membres du club.</p>
                <div class="pt-2 border-t border-gray-200 dark:border-gray-700 space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Callback URL</span>
                        <code class="text-xs bg-gray-100 dark:bg-gray-800 px-2 py-0.5 rounded">{{ route('strava.callback') }}</code>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Client ID</span>
                        @if(config('association.strava_client_id'))
                            <span class="font-mono text-gray-900 dark:text-gray-100">{{ config('association.strava_client_id') }}</span>
                        @else
                            <span class="text-red-500">non configuré</span>
                        @endif
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Client Secret</span>
                        @if(config('association.strava_client_secret'))
                            <span class="font-mono text-gray-900 dark:text-gray-100">{{ str_repeat('•', 8) }}{{ substr(config('association.strava_client_secret'), -4) }}</span>
                        @else
                            <span class="text-red-500">non configuré</span>
                        @endif
                    </div>
                </div>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
