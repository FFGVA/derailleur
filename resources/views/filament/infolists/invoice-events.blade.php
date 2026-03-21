@php
    $events = $getRecord()->events;
@endphp

@if($events->isEmpty())
    <span class="text-gray-400 text-sm">Aucun événement</span>
@else
    <div class="space-y-1.5">
        @foreach($events as $event)
            <div class="flex items-center justify-between py-1 {{ !$loop->last ? 'border-b border-gray-100 dark:border-gray-700' : '' }}">
                <a href="{{ \App\Filament\Resources\EventResource::getUrl('view', ['record' => $event]) }}"
                   class="text-sm text-primary-600 hover:underline">
                    {{ $event->title }}
                </a>
                <span class="text-xs text-gray-400">{{ $event->starts_at->format('d.m.Y') }}</span>
            </div>
        @endforeach
    </div>
@endif
