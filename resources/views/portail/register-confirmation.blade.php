@extends('portail.layout')

@section('title', 'Compte créé')

@section('styles')
    .portal-confirm-wrapper {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        min-height: 60vh;
        padding: 1.5rem 1rem;
    }
    .portal-confirm-icon {
        width: 4rem;
        height: 4rem;
        color: #16a34a;
        margin-bottom: 1rem;
    }
    .portal-confirm-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #333;
        margin-bottom: 0.5rem;
    }
    .portal-confirm-text {
        font-size: 0.9375rem;
        color: #666;
        max-width: 360px;
        line-height: 1.6;
    }
@endsection

@section('header')
    <header class="portal-header" style="justify-content: center;">
        <span class="portal-brand">{{ config('association.name') }}</span>
    </header>
@endsection

@section('content')
    <div class="portal-confirm-wrapper">
        <svg class="portal-confirm-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div class="portal-confirm-title">Vérifie ta boîte mail</div>
        <div class="portal-confirm-text">
            Si cette adresse est valide, tu recevras un lien de connexion dans quelques instants. Clique dessus pour accéder à ton espace.
        </div>
    </div>
@endsection
