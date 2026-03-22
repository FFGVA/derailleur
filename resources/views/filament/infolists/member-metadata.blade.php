@php
    $metadata = $getRecord()->metadata;
@endphp

@if(empty($metadata))
    <span class="text-gray-400">—</span>
@else
    <div class="space-y-1">
        @foreach($metadata as $key => $value)
            <div class="flex gap-2 text-sm">
                <span class="font-medium text-gray-500 min-w-[120px]">{{ $key }}</span>
                <span class="text-gray-900">{{ is_bool($value) ? ($value ? 'Oui' : 'Non') : $value }}</span>
            </div>
        @endforeach
    </div>
@endif
