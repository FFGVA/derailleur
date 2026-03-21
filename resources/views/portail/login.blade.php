@extends('portail.layout')

@section('title', 'Connexion')

@section('styles')
    .portal-login-wrapper {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        min-height: 60vh;
    }
    .portal-login-logo {
        max-width: 160px;
        margin-bottom: 2rem;
    }
    .portal-login-form {
        width: 100%;
        max-width: 360px;
    }
    .portal-input {
        width: 100%;
        padding: 0.75rem 1rem;
        font-size: 1rem;
        border: 2px solid #ddd;
        border-radius: 0.5rem;
        background-color: white;
        color: #333;
        margin-bottom: 0.75rem;
        text-align: center;
    }
    .portal-input:focus {
        outline: none;
        border-color: #80081C;
    }
    .portal-submit {
        width: 100%;
        background-color: #80081C;
        color: white;
        font-weight: 600;
        font-size: 1rem;
        padding: 0.875rem 2rem;
        border: none;
        border-radius: 0.5rem;
        cursor: pointer;
        transition: background-color 0.2s;
    }
    .portal-submit:hover {
        background-color: #660614;
    }
    .portal-flash {
        width: 100%;
        max-width: 360px;
        padding: 0.75rem 1rem;
        border-radius: 0.5rem;
        margin-bottom: 1rem;
        font-size: 0.9375rem;
    }
    .portal-flash-success {
        background-color: #d1fae5;
        color: #065f46;
        border: 1px solid #6ee7b7;
    }
    .portal-flash-error {
        background-color: #fee2e2;
        color: #991b1b;
        border: 1px solid #fca5a5;
    }
    .portal-validation-error {
        color: #dc2626;
        font-size: 0.875rem;
        margin-bottom: 0.5rem;
    }
@endsection

@section('header')
    <header class="portal-header" style="justify-content: center;">
        <span class="portal-brand">Fast and Female Geneva</span>
    </header>
@endsection

@section('content')
    <div class="portal-login-wrapper">
        <img src="/images/logo-ffgva.png" alt="Fast and Female Geneva" class="portal-login-logo">

        @if (session('magic_link_success'))
            <div class="portal-flash portal-flash-success">
                Un lien de connexion a été envoyé à <strong>{{ session('magic_link_email') }}</strong>.
                Vérifie ta boîte de réception.
            </div>
        @endif

        @if (session('magic_link_error'))
            <div class="portal-flash portal-flash-error">
                {{ session('magic_link_error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('portail.send-link') }}" class="portal-login-form">
            @csrf
            <input type="email" name="email" placeholder="Ton adresse e-mail" value="{{ old('email') }}" class="portal-input" required>
            @error('email')
                <p class="portal-validation-error">{{ $message }}</p>
            @enderror
            <button type="submit" class="portal-submit">Recevoir un lien de connexion</button>
        </form>
    </div>
@endsection
