@php
    $metadata = $getRecord()->metadata;
    $labels = \App\Models\Member::METADATA_LABELS;
@endphp

@if(empty($metadata))
    <span class="text-gray-400">—</span>
@else
    <table class="w-full text-sm">
        @foreach($metadata as $key => $value)
            @if(in_array($key, ['instagram', 'strava']))
                @continue
            @endif
            <tr class="border-b border-gray-100 dark:border-gray-700">
                <td class="py-1.5 pr-4 align-top" style="width: 40%;">
                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ $labels[$key] ?? $key }}</span>
                    @if(isset($labels[$key]))
                        <span class="text-gray-400 text-xs ml-1">{{ $key }}</span>
                    @endif
                </td>
                <td class="py-1.5 text-gray-700 dark:text-gray-300">
                    @if(is_bool($value))
                        {{ $value ? 'Oui' : 'Non' }}
                    @elseif(is_null($value) || $value === '')
                        <span class="text-gray-400">—</span>
                    @else
                        {{ $value }}
                    @endif
                </td>
            </tr>
        @endforeach
    </table>
@endif
