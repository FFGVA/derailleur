@extends('portail.layout')

@section('title', 'Cheffe de peloton')

@section('styles')
    .portal-event-card {
        background: white;
        border-radius: 0.5rem;
        padding: 1rem 1.25rem;
        margin-bottom: 0.625rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        text-decoration: none;
        display: block;
        color: #333;
        transition: box-shadow 0.2s;
    }
    .portal-event-card:hover {
        box-shadow: 0 2px 8px rgba(0,0,0,0.12);
    }
    .portal-event-title {
        font-size: 0.9375rem;
        font-weight: 600;
        color: #333;
    }
    .portal-event-meta {
        font-size: 0.8125rem;
        color: #666;
        margin-top: 0.25rem;
    }
    .portal-event-count {
        font-size: 0.75rem;
        color: #80081C;
        font-weight: 500;
        margin-top: 0.25rem;
    }
    .portal-badge {
        display: inline-block;
        font-size: 0.6875rem;
        font-weight: 600;
        padding: 0.125rem 0.5rem;
        border-radius: 0.25rem;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }
    .portal-badge-green { background-color: #dcfce7; color: #166534; }
    .portal-badge-orange { background-color: #fff7ed; color: #9a3412; }
    .portal-badge-red { background-color: #fef2f2; color: #991b1b; }
    .portal-badge-blue { background-color: #dbeafe; color: #1e40af; }
    .portal-badge-grey { background-color: #f5f5f4; color: #57534e; }
    .portal-empty {
        text-align: center;
        padding: 2rem 1rem;
        color: #999;
        font-size: 0.9375rem;
    }
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
