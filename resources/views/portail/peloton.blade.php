@extends('portail.layout')

@section('title', 'Cheffe de peloton')

@section('styles')
    .portal-event-card {
        color: #333;
    }
    .portal-event-count {
        font-size: 0.75rem;
        color: #80081C;
        font-weight: 500;
        margin-top: 0.25rem;
    }
    .portal-badge-grey { background-color: #f5f5f4; color: #57534e; }
@endsection

@section('header')
    <header class="portal-header">
        <span class="portal-brand">Cheffe de peloton</span>
        <a href="{{ route('portail.dashboard') }}" class="portal-header-action">Retour</a>
    </header>
@endsection

@section('content')
    @forelse($events as $event)
        <a href="{{ route('portail.peloton.event', $event) }}" class="portal-event-card">
            <div class="portal-event-title">{{ $event->title }}</div>
            <div class="portal-event-meta">
                {{ $event->starts_at->format('d.m.Y H:i') }}
                @if($event->location) · {{ $event->location }}@endif
                · <span class="portal-badge {{ match($event->statuscode->value) { 'P' => 'portal-badge-green', 'N' => 'portal-badge-blue', 'X' => 'portal-badge-red', 'T' => 'portal-badge-grey', default => '' } }}">{{ $event->statuscode->getLabel() }}</span>
            </div>
            @php $count = $event->members()->whereNull('event_member.deleted_at')->count(); @endphp
            <div class="portal-event-count">{{ $count }} participant{{ $count > 1 ? 's' : '' }}</div>
        </a>
    @empty
        <div class="portal-empty">Aucun événement.</div>
    @endforelse
@endsection
