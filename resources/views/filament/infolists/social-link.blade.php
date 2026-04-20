@php
    $record = $getRecord();
    $url = is_callable($getUrl) ? $getUrl($record) : $getUrl;
    $text = is_callable($getText) ? $getText($record) : $getText;
@endphp

@if($text)
    <div style="display: flex; align-items: center; gap: 0.5rem;">
        <img src="{{ $icon }}" style="width: 1.25rem; height: 1.25rem; flex-shrink: 0;">
        @if($url)
            <a href="{{ $url }}" target="_blank" rel="noopener" style="color: {{ config('association.colors.primary') }}; text-decoration: none; font-size: 0.875rem;">{{ $text }}</a>
        @else
            <span style="font-size: 0.875rem; color: #333;">{{ $text }}</span>
        @endif
    </div>
@else
    <span style="color: #999; font-size: 0.875rem;">—</span>
@endif
