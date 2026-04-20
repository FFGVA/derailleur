@extends('portail.layout')

@section('title', 'Devenir membre')

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
    .portal-intro-card {
        background: linear-gradient(135deg, #80081C 0%, #a30d25 100%);
        border-radius: 0.75rem;
        padding: 1.25rem;
        margin-bottom: 1.5rem;
        color: white;
        box-shadow: 0 2px 8px rgba(128,8,28,0.3);
    }
    .portal-intro-title {
        font-size: 1.125rem;
        font-weight: 700;
        margin-bottom: 0.375rem;
    }
    .portal-intro-text {
        font-size: 0.875rem;
        opacity: 0.9;
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
    .portal-select {
        width: 100%;
        padding: 0.75rem 1rem;
        font-size: 1rem;
        border: 2px solid #ddd;
        border-radius: 0.5rem;
        background-color: white;
        color: #333;
        margin-bottom: 1rem;
        box-sizing: border-box;
        appearance: auto;
    }
    .portal-select:focus {
        outline: none;
        border-color: #80081C;
    }
    .portal-radio-group {
        margin-bottom: 1rem;
    }
    .portal-radio-row {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.375rem;
    }
    .portal-radio-row input[type="radio"] {
        width: 1.125rem;
        height: 1.125rem;
        accent-color: #80081C;
    }
    .portal-radio-row label {
        font-size: 0.9375rem;
        color: #333;
    }
    .portal-checkbox-row {
        display: flex;
        align-items: flex-start;
        gap: 0.5rem;
        margin-bottom: 1rem;
    }
    .portal-checkbox-row input[type="checkbox"] {
        width: 1.125rem;
        height: 1.125rem;
        accent-color: #80081C;
        margin-top: 0.125rem;
        flex-shrink: 0;
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
    .portal-section-label {
        font-size: 0.875rem;
        font-weight: 700;
        color: #333;
        margin-bottom: 0.75rem;
        padding-top: 0.5rem;
        border-top: 1px solid #eee;
    }
@endsection

@section('header')
    <header class="portal-header">
        <span class="portal-brand">Adhésion</span>
    </header>
@endsection

@section('content')
    <div class="portal-reg-wrapper">
        <div class="portal-reg-form">
            <a href="{{ route('portail.dashboard') }}" class="portal-back">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Retour
            </a>

            <div class="portal-intro-card">
                <div class="portal-intro-title">Deviens membre !</div>
                <div class="portal-intro-text">Soutiens notre association et profite des événements et avantages réservés aux membres. La cotisation annuelle est de CHF {{ number_format((float) config('association.cotisation_annuelle'), 2, '.', '') }}.</div>
            </div>

            <form method="POST" action="{{ route('portail.adhesion.inscription.store') }}">
                @csrf

                <div class="portal-field-label">Prénom</div>
                <input type="text" name="prenom" class="portal-input" value="{{ old('prenom', $member->first_name) }}" required>
                @error('prenom')<div class="portal-error">{{ $message }}</div>@enderror

                <div class="portal-field-label">Nom</div>
                <input type="text" name="nom" class="portal-input" value="{{ old('nom', $member->last_name) }}" required>
                @error('nom')<div class="portal-error">{{ $message }}</div>@enderror

                <div class="portal-field-label">E-mail</div>
                <input type="email" name="email" class="portal-input portal-input-readonly" value="{{ $member->email }}" readonly>

                <div class="portal-field-label">Téléphone</div>
                <input type="tel" name="telephone" class="portal-input" value="{{ old('telephone', $member->phones->first()?->phone_number) }}" required>
                @error('telephone')<div class="portal-error">{{ $message }}</div>@enderror

                <div class="portal-section-label">Questionnaire</div>

                <div class="portal-field-label">Quel type de vélo as-tu ?</div>
                <select name="type_velo" class="portal-select">
                    <option value="">Choisir...</option>
                    <option value="route" {{ old('type_velo') === 'route' ? 'selected' : '' }}>Vélo de route</option>
                    <option value="gravel" {{ old('type_velo') === 'gravel' ? 'selected' : '' }}>Gravel</option>
                    <option value="vtt" {{ old('type_velo') === 'vtt' ? 'selected' : '' }}>VTT</option>
                    <option value="electrique" {{ old('type_velo') === 'electrique' ? 'selected' : '' }}>Électrique</option>
                    <option value="autre" {{ old('type_velo') === 'autre' ? 'selected' : '' }}>Autre</option>
                </select>

                <div class="portal-field-label">Combien de sorties fais-tu par semaine ?</div>
                <select name="sorties" class="portal-select">
                    <option value="">Choisir...</option>
                    <option value="0-1" {{ old('sorties') === '0-1' ? 'selected' : '' }}>0–1</option>
                    <option value="2-3" {{ old('sorties') === '2-3' ? 'selected' : '' }}>2–3</option>
                    <option value="4+" {{ old('sorties') === '4+' ? 'selected' : '' }}>4 ou plus</option>
                </select>

                <div class="portal-field-label">Es-tu intéressée par des ateliers mécaniques ?</div>
                <div class="portal-radio-group">
                    <div class="portal-radio-row">
                        <input type="radio" id="atelier_oui" name="atelier" value="oui" {{ old('atelier') === 'oui' ? 'checked' : '' }}>
                        <label for="atelier_oui">Oui</label>
                    </div>
                    <div class="portal-radio-row">
                        <input type="radio" id="atelier_non" name="atelier" value="non" {{ old('atelier') === 'non' ? 'checked' : '' }}>
                        <label for="atelier_non">Non</label>
                    </div>
                </div>

                <div class="portal-field-label">Instagram (optionnel)</div>
                <input type="text" name="instagram" class="portal-input" value="{{ old('instagram') }}" placeholder="@compte">

                <div class="portal-field-label">Strava (optionnel)</div>
                <input type="text" name="strava" class="portal-input" value="{{ old('strava') }}" placeholder="Lien profil ou nom">

                <div class="portal-field-label">Autorisation photos / vidéos</div>
                <div class="portal-radio-group">
                    <div class="portal-radio-row">
                        <input type="radio" id="photo_oui" name="photo_ok" value="oui" {{ old('photo_ok', 'oui') === 'oui' ? 'checked' : '' }}>
                        <label for="photo_oui">J'accepte</label>
                    </div>
                    <div class="portal-radio-row">
                        <input type="radio" id="photo_non" name="photo_ok" value="non" {{ old('photo_ok') === 'non' ? 'checked' : '' }}>
                        <label for="photo_non">Je refuse</label>
                    </div>
                </div>

                <div class="portal-checkbox-row">
                    <input type="checkbox" id="statuts_ok" name="statuts_ok" value="oui" {{ old('statuts_ok') ? 'checked' : '' }} required>
                    <label for="statuts_ok">J'ai lu et j'accepte les <a href="{{ route('portail.lpd') }}" style="color: #80081C;">statuts de l'association</a></label>
                </div>
                @error('statuts_ok')<div class="portal-error">{{ $message }}</div>@enderror

                <div class="portal-checkbox-row">
                    <input type="checkbox" id="cotisation_ok" name="cotisation_ok" value="oui" {{ old('cotisation_ok') ? 'checked' : '' }} required>
                    <label for="cotisation_ok">J'accepte de payer la cotisation annuelle de CHF {{ number_format((float) config('association.cotisation_annuelle'), 2, '.', '') }}</label>
                </div>
                @error('cotisation_ok')<div class="portal-error">{{ $message }}</div>@enderror

                <button type="submit" class="portal-submit">Je m'inscris !</button>
            </form>
        </div>
    </div>
@endsection
