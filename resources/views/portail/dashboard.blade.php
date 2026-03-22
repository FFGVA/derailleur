@extends('portail.layout')

@section('title', 'Mon espace')

@section('styles')
    .portal-welcome-card {
        background: white;
        border-radius: 0.75rem;
        padding: 1.5rem;
        margin-bottom: 1rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        text-align: center;
    }
    .portal-welcome-name {
        font-size: 1.375rem;
        font-weight: 700;
        color: #80081C;
        margin-bottom: 0.25rem;
    }
    .portal-welcome-phone {
        font-size: 0.9375rem;
        color: #666;
    }
    .portal-nav-buttons {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.75rem;
        margin-bottom: 1.5rem;
    }
    .portal-nav-buttons.cols-3 {
        grid-template-columns: 1fr 1fr 1fr;
    }
    .portal-nav-btn {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.375rem;
        background: white;
        border-radius: 0.75rem;
        padding: 1.25rem 0.75rem;
        text-decoration: none;
        color: #333;
        font-weight: 600;
        font-size: 0.9375rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        transition: box-shadow 0.2s;
    }
    .portal-nav-btn:hover {
        box-shadow: 0 2px 8px rgba(0,0,0,0.12);
    }
    .portal-nav-icon {
        width: 2rem;
        height: 2rem;
        color: #80081C;
    }
    .portal-section-title {
        font-size: 1rem;
        font-weight: 700;
        color: #333;
        margin-bottom: 0.75rem;
    }
    .portal-event-card {
        display: block;
        background: white;
        border-radius: 0.5rem;
        padding: 1rem 1.25rem;
        margin-bottom: 0.625rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        text-decoration: none;
        color: inherit;
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
    .portal-empty {
        text-align: center;
        padding: 2rem 1rem;
        color: #999;
        font-size: 0.9375rem;
    }
@endsection

@section('header')
    <header class="portal-header">
        <span class="portal-brand">Mon espace</span>
        <form method="POST" action="{{ route('portail.logout') }}">
            @csrf
            <button type="submit" class="portal-header-action">Déconnexion</button>
        </form>
    </header>
@endsection

@section('content')
    <a href="{{ route('portail.adhesion') }}" class="portal-welcome-card" style="text-decoration: none; color: inherit; display: block;">
        <div class="portal-welcome-name">{{ $member->first_name }} {{ $member->last_name }}</div>
        @if($member->phones->isNotEmpty())
            <div class="portal-welcome-phone">
                {{ $member->phones->first()->phone_number }}
            </div>
        @endif
    </a>

    <div class="portal-nav-buttons{{ $isChef ? ' cols-3' : '' }}">
        @if($isChef)
            <a href="{{ route('portail.peloton') }}" class="portal-nav-btn">
                <svg class="portal-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                </svg>
                Peloton
            </a>
        @endif
        <a href="{{ route('portail.adhesion') }}" class="portal-nav-btn">
            <svg class="portal-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            Adhésion
        </a>
        <a href="{{ route('portail.factures') }}" class="portal-nav-btn">
            <svg class="portal-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Factures
        </a>
    </div>

    <h2 class="portal-section-title">Prochains événements</h2>

    @forelse($upcomingEvents as $event)
        <a href="{{ route('portail.evenement', $event) }}" class="portal-event-card">
            <div class="portal-event-title">{{ $event->title }}</div>
            <div class="portal-event-meta">
                {{ $event->starts_at->format('d.m.Y') }}
                @if($event->location) · {{ $event->location }}@endif
            </div>
            @if($event->memberRegistration && $event->memberRegistration->status->value !== 'X')
                <div class="portal-event-meta">
                    Mon statut : <span class="portal-badge {{ match($event->memberRegistration->status->value) { 'C' => 'portal-badge-green', 'N' => 'portal-badge-orange', default => '' } }}">{{ $event->memberRegistration->status->getLabel() }}</span>
                </div>
            @endif
        </a>
    @empty
        <div class="portal-empty">Aucun événement à venir.</div>
    @endforelse
@endsection
