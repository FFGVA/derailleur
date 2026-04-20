@extends('portail.layout')

@section('title', 'Mon adhésion')

@section('styles')
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
    .portal-edit-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        width: 100%;
        padding: 0.875rem;
        margin-top: 0.5rem;
        background-color: white;
        color: #991b1b;
        font-weight: 600;
        font-size: 0.9375rem;
        border: 2px solid #991b1b;
        border-radius: 0.5rem;
        text-decoration: none;
        text-align: center;
        transition: all 0.2s;
    }
    .portal-edit-btn:hover {
        background-color: #991b1b;
        color: white;
    }
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
    @if(session('success'))
        <div style="background-color: #d1fae5; color: #065f46; border: 1px solid #6ee7b7; border-radius: 0.5rem; padding: 0.75rem 1rem; margin-bottom: 1rem; font-size: 0.9375rem;">
            {{ session('success') }}
        </div>
    @endif

    <div class="portal-info-card">
        @if($member->member_number)
            <div class="portal-info-row">
                <span class="portal-info-label">N° membre</span>
                <span class="portal-info-value">{{ $member->member_number }}</span>
            </div>
        @endif

        <div class="portal-info-row">
            <span class="portal-info-label">Statut</span>
            @php
                $expired = $member->membership_end && $member->membership_end->isPast();
            @endphp
            <span class="portal-info-value">
                @if($expired)
                    <span class="portal-badge portal-badge-red">Inactive</span>
                @else
                    <span class="portal-badge {{ $member->statuscode->value === 'A' ? 'portal-badge-green' : 'portal-badge-orange' }}">
                        {{ $member->statuscode->getLabel() }}
                    </span>
                @endif
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

        @foreach($member->phones as $phone)
            <div class="portal-info-row">
                <span class="portal-info-label">{{ $phone->label ?? 'Téléphone' }}</span>
                <span class="portal-info-value">
                    @if($phone->is_whatsapp)
                        <svg style="width: 0.875rem; height: 0.875rem; vertical-align: middle; margin-right: 0.25rem; color: #999;" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    @endif
                    <a href="tel:{{ $phone->phone_number }}" style="color: #333; text-decoration: none;">{{ $phone->phone_number }}</a>
                </span>
            </div>
        @endforeach

        @if($member->membership_start)
            <div class="portal-info-row">
                <span class="portal-info-label">Membre depuis</span>
                <span class="portal-info-value">{{ $member->membership_start->format('d.m.Y') }}</span>
            </div>
        @endif

        @if($member->membership_end)
            <div class="portal-info-row">
                <span class="portal-info-label">Fin d'adhésion</span>
                <span class="portal-info-value"{{ $expired ? ' style="color: #991b1b;"' : '' }}>{{ $member->membership_end->format('d.m.Y') }}</span>
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

    <a href="mailto:{{ config('association.contact_email') }}" class="portal-contact-btn">
        <svg style="width:1.125rem;height:1.125rem;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
        </svg>
        Envoyer un message au comité
    </a>
    <a href="{{ route('portail.adhesion.edit') }}" class="portal-edit-btn">
        <svg style="width:1.125rem;height:1.125rem;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
        </svg>
        Modifier mes informations
    </a>

    <div style="text-align: center; margin-top: 1.5rem;">
        <a href="{{ route('portail.lpd') }}" style="color: #999; font-size: 0.8125rem; text-decoration: none;">Protection des données</a>
    </div>
@endsection
