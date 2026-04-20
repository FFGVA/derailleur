@extends('portail.layout')

@section('title', 'Créer un compte')

@section('styles')
    .portal-reg-wrapper {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 1.5rem 1rem;
    }
    .portal-reg-form {
        width: 100%;
        max-width: 420px;
    }
    .portal-reg-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #333;
        margin-bottom: 0.25rem;
        text-align: center;
    }
    .portal-reg-subtitle {
        font-size: 0.875rem;
        color: #666;
        text-align: center;
        margin-bottom: 1.5rem;
    }
    .portal-field-label {
        display: block;
        font-size: 0.875rem;
        font-weight: 500;
        color: #333;
        margin-bottom: 0.25rem;
    }
    .portal-input {
        width: 100%;
        padding: 0.625rem 0.875rem;
        border: 1px solid #ddd;
        border-radius: 0.5rem;
        font-size: 0.9375rem;
        color: #333;
        background: white;
        margin-bottom: 1rem;
    }
    .portal-input:focus {
        outline: none;
        border-color: var(--color-primary);
        box-shadow: 0 0 0 3px rgba(128,8,28,0.1);
    }
    .portal-submit {
        width: 100%;
        padding: 0.75rem;
        background-color: var(--color-primary);
        color: white;
        font-weight: 600;
        font-size: 1rem;
        border: none;
        border-radius: 0.5rem;
        cursor: pointer;
        margin-top: 0.5rem;
    }
    .portal-submit:hover {
        background-color: var(--color-primary-hover);
    }
    .portal-error {
        color: #dc2626;
        font-size: 0.8125rem;
        margin-top: -0.75rem;
        margin-bottom: 0.75rem;
    }
    .portal-login-link {
        text-align: center;
        margin-top: 1.5rem;
        font-size: 0.875rem;
    }
    .portal-login-link a {
        color: var(--color-primary);
        text-decoration: none;
        font-weight: 500;
    }
    .offscreen { position: absolute; left: -9999px; }
@endsection

@section('header')
    <header class="portal-header" style="justify-content: center;">
        <span class="portal-brand">{{ config('association.name') }}</span>
    </header>
@endsection

@section('content')
    <div class="portal-reg-wrapper">
        <img src="{{ asset(config('association.logo_path')) }}" alt="{{ config('association.name') }}" style="max-width: 120px; margin-bottom: 1.5rem;">

        <div class="portal-reg-form">
            <div class="portal-reg-title">Créer un compte</div>
            <div class="portal-reg-subtitle">Accède aux événements et activités de l'association.</div>

            <form method="POST" action="{{ route('register.store') }}">
                @csrf

                <label class="portal-field-label" for="prenom">Prénom</label>
                <input type="text" id="prenom" name="prenom" value="{{ old('prenom') }}" class="portal-input" required>
                @error('prenom') <p class="portal-error">{{ $message }}</p> @enderror

                <label class="portal-field-label" for="nom">Nom</label>
                <input type="text" id="nom" name="nom" value="{{ old('nom') }}" class="portal-input" required>
                @error('nom') <p class="portal-error">{{ $message }}</p> @enderror

                <label class="portal-field-label" for="email">Adresse e-mail</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" class="portal-input" required>
                @error('email') <p class="portal-error">{{ $message }}</p> @enderror

                <label class="portal-field-label" for="telephone">Téléphone</label>
                <input type="tel" id="telephone" name="telephone" value="{{ old('telephone') }}" class="portal-input" required>
                @error('telephone') <p class="portal-error">{{ $message }}</p> @enderror

                {{-- Honeypot --}}
                <div class="offscreen" aria-hidden="true">
                    <input type="text" name="website" tabindex="-1" autocomplete="off">
                </div>

                <button type="submit" class="portal-submit">Créer mon compte</button>
            </form>

            <div class="portal-login-link">
                Déjà un compte ? <a href="{{ route('portail.login') }}">Se connecter</a>
            </div>
        </div>
    </div>
@endsection
