@extends('portail.layout')

@section('title', 'Inscription — ' . $event->title)

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
    .portal-event-card {
        background: white;
        border-radius: 0.75rem;
        padding: 1.25rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    }
    .portal-event-title {
        font-size: 1.125rem;
        font-weight: 700;
        color: #333;
        margin-bottom: 0.5rem;
    }
    .portal-event-detail {
        font-size: 0.9375rem;
        color: #666;
        margin-bottom: 0.25rem;
    }
    .portal-field-label {
        font-size: 0.8125rem;
        font-weight: 600;
        color: #666;
        margin-bottom: 0.25rem;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }
    .portal-input {
        width: 100%;
        padding: 0.75rem 1rem;
        font-size: 1rem;
        border: 2px solid #ddd;
        border-radius: 0.5rem;
        background-color: white;
        color: #333;
        margin-bottom: 1rem;
        box-sizing: border-box;
    }
    .portal-input:focus {
        outline: none;
        border-color: #80081C;
    }
    .portal-input-readonly {
        background-color: #f5f1e9;
        color: #999;
    }
    .portal-checkbox-row {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 1rem;
    }
    .portal-checkbox-row input[type="checkbox"] {
        width: 1.125rem;
        height: 1.125rem;
        accent-color: #80081C;
    }
    .portal-checkbox-row label {
        font-size: 0.9375rem;
        color: #333;
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
    .portal-error {
        color: #991b1b;
        font-size: 0.8125rem;
        margin-top: -0.75rem;
        margin-bottom: 0.75rem;
    }
@endsection

@section('header')
    <header class="portal-header">
        <span class="portal-brand">Inscription</span>
    </header>
@endsection

@section('content')
    <div class="portal-reg-wrapper">
        <div class="portal-reg-form">
            <div class="portal-event-card">
                <div class="portal-event-title">{{ $event->title }}</div>
                <div class="portal-event-detail">{{ $event->starts_at->format('d.m.Y à H:i') }}</div>
                @if($event->location)
                    <div class="portal-event-detail">{{ $event->location }}</div>
                @endif
                @if((float) $price > 0)
                    <div class="portal-event-detail" style="font-weight: 600; color: #333; margin-top: 0.5rem;">CHF {{ number_format((float) $price, 2, '.', '') }}</div>
                @endif
            </div>

            <form method="POST" action="{{ request()->fullUrl() }}">
                @csrf
                <input type="hidden" name="event_id" value="{{ $event->id }}">

                <div class="portal-field-label">Prénom</div>
                <input type="text" name="prenom" class="portal-input" value="{{ old('prenom') }}" required>
                @error('prenom')<div class="portal-error">{{ $message }}</div>@enderror

                <div class="portal-field-label">Nom</div>
                <input type="text" name="nom" class="portal-input" value="{{ old('nom') }}" required>
                @error('nom')<div class="portal-error">{{ $message }}</div>@enderror

                <div class="portal-field-label">E-mail</div>
                <input type="email" name="email" class="portal-input portal-input-readonly" value="{{ $email }}" readonly>

                <div class="portal-field-label">Téléphone</div>
                <input type="tel" name="telephone" class="portal-input" value="{{ old('telephone') }}" required>
                @error('telephone')<div class="portal-error">{{ $message }}</div>@enderror

                <div class="portal-checkbox-row">
                    <input type="checkbox" id="whatsapp" name="whatsapp" value="1" {{ old('whatsapp') ? 'checked' : '' }}>
                    <label for="whatsapp">Ce numéro est aussi WhatsApp</label>
                </div>

                <button type="submit" class="portal-submit">M'inscrire</button>
            </form>
        </div>
    </div>
@endsection
