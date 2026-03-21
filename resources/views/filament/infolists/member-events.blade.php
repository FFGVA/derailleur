@php
    $member = $getRecord();
    $member->load(['events', 'ledEvents']);

    $allEvents = $member->events
        ->merge($member->ledEvents)
        ->unique('id')
        ->sortBy('starts_at');
@endphp

@if($allEvents->isEmpty())
    <span class="text-gray-400 text-sm">Aucune activité</span>
@else
    <table class="w-full text-sm">
        @foreach($allEvents as $event)
            <tr class="{{ !$loop->last ? 'border-b border-gray-100 dark:border-gray-700' : '' }}">
                <td class="py-1.5 pr-2">
                    <a href="{{ \App\Filament\Resources\EventResource::getUrl('edit', ['record' => $event]) }}"
                       class="text-primary-600 hover:underline">
                        {{ $event->title }}
                    </a>
                </td>
                <td class="py-1.5 text-right text-xs text-gray-400 whitespace-nowrap">
                    {{ $event->starts_at->format('d.m.Y') }}
                </td>
                <td class="py-1.5 pl-2 w-5">
                    @if($event->chef_peloton_id === $member->id)
                        <x-heroicon-s-star class="w-4 h-4 text-warning-500" title="Cheffe de peloton" />
                    @endif
                </td>
            </tr>
        @endforeach
    </table>
@endif
