<x-filament-widgets::widget>
    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        @forelse($this->getEvents() as $event)
            <a href="{{ \App\Filament\Resources\EventResource::getUrl('edit', ['record' => $event]) }}" class="block transition hover:scale-[1.02]">
                <x-filament::section>
                    <x-slot name="heading">
                        {{ $event->title }}
                    </x-slot>

                    <div class="space-y-2 text-sm">
                        <div class="flex items-center gap-2 text-gray-600 dark:text-gray-400">
                            <x-heroicon-o-calendar-days class="w-4 h-4" />
                            <span>{{ $event->starts_at->format('d.m.Y H:i') }}</span>
                            @if($event->ends_at)
                                <span>— {{ $event->ends_at->format('H:i') }}</span>
                            @endif
                        </div>

                        @if($event->location)
                            <div class="flex items-center gap-2 text-gray-600 dark:text-gray-400">
                                <x-heroicon-o-map-pin class="w-4 h-4" />
                                <span>{{ $event->location }}</span>
                            </div>
                        @endif

                        @if($event->chefPeloton)
                            <div class="flex items-center gap-2 text-gray-600 dark:text-gray-400">
                                <x-heroicon-o-user class="w-4 h-4" />
                                <span>{{ $event->chefPeloton->first_name }} {{ $event->chefPeloton->last_name }}</span>
                            </div>
                        @endif

                        <div class="flex items-center justify-between pt-2">
                            <x-filament::badge
                                :color="$event->statuscode->getColor()"
                            >
                                {{ $event->statuscode->getLabel() }}
                            </x-filament::badge>

                            @if($event->members()->count())
                                <span class="text-xs text-gray-500">
                                    {{ $event->members()->count() }} participante(s)
                                </span>
                            @endif
                        </div>
                    </div>
                </x-filament::section>
            </a>
        @empty
            <div class="col-span-full text-center text-gray-500 py-8">
                Aucun événement à venir
            </div>
        @endforelse
    </div>
</x-filament-widgets::widget>
