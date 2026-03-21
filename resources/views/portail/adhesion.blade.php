@extends('portail.layout')

@section('title', 'Mon adhésion')

@section('styles')
    .portal-back {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        color: #80081C;
        text-decoration: none;
        font-size: 0.875rem;
        font-weight: 500;
        margin-bottom: 1rem;
    }
    .portal-back svg {
        width: 1rem;
        height: 1rem;
    }
    .portal-info-card {
        background: white;
        border-radius: 0.75rem;
        padding: 1.25rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    }
    .portal-info-row {
        display: flex;
        justify-content: space-between;
        padding: 0.625rem 0;
        border-bottom: 1px solid #f0ede8;
        font-size: 0.9375rem;
    }
    .portal-info-row:last-child {
        border-bottom: none;
    }
    .portal-info-label {
        color: #666;
    }
    .portal-info-value {
        font-weight: 500;
        color: #333;
        text-align: right;
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
    .portal-contact-btn {
        margin-top: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        width: 100%;
        padding: 0.875rem;
        background-color: #80081C;
        color: white;
        font-weight: 600;
        font-size: 0.9375rem;
        border: none;
        border-radius: 0.5rem;
        text-decoration: none;
        text-align: center;
        transition: background-color 0.2s;
    }
    .portal-contact-btn:hover {
        background-color: #660614;
    }
@endsection

@section('header')
    <header class="portal-header">
        <span class="portal-brand">Mon adhésion</span>
        <a href="{{ route('portail.dashboard') }}" class="portal-header-action">Retour</a>
    </header>
@endsection

@section('content')
    <div class="portal-info-card">
        @if($member->member_number)
            <div class="portal-info-row">
                <span class="portal-info-label">N° membre</span>
                <span class="portal-info-value">{{ $member->member_number }}</span>
            </div>
        @endif

        <div class="portal-info-row">
            <span class="portal-info-label">Statut</span>
            <span class="portal-info-value">
                <span class="portal-badge {{ $member->statuscode->value === 'A' ? 'portal-badge-green' : 'portal-badge-orange' }}">
                    {{ $member->statuscode->getLabel() }}
                </span>
            </span>
        </div>

        <div class="portal-info-row">
            <span class="portal-info-label">Nom</span>
            <span class="portal-info-value">{{ $member->first_name }} {{ $member->last_name }}</span>
        </div>

        <div class="portal-info-row">
            <span class="portal-info-label">E-mail</span>
            <span class="portal-info-value">{{ $member->email }}</span>
        </div>

        @if($member->membership_start)
            <div class="portal-info-row">
                <span class="portal-info-label">Membre depuis</span>
                <span class="portal-info-value">{{ $member->membership_start->format('d.m.Y') }}</span>
            </div>
        @endif

        @if($member->membership_end)
            <div class="portal-info-row">
                <span class="portal-info-label">Fin d'adhésion</span>
                <span class="portal-info-value">{{ $member->membership_end->format('d.m.Y') }}</span>
            </div>
        @endif

        @if($member->address)
            <div class="portal-info-row">
                <span class="portal-info-label">Adresse</span>
                <span class="portal-info-value">
                    {{ $member->address }}<br>
                    {{ $member->postal_code }} {{ $member->city }}
                </span>
            </div>
        @endif
    </div>

    <a href="mailto:{{ config('ffgva.contact_email') }}" class="portal-contact-btn">
        <svg style="width:1.125rem;height:1.125rem;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
        </svg>
        Envoyer un message au comité
    </a>
@endsection
