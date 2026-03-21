@extends('portail.layout')

@section('title', 'Modifier mes informations')

@section('styles')
    .portal-form-card {
        background: white;
        border-radius: 0.75rem;
        padding: 1.25rem;
        margin-bottom: 1rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    }
    .portal-form-group {
        margin-bottom: 1rem;
    }
    .portal-form-group:last-child {
        margin-bottom: 0;
    }
    .portal-form-label {
        display: block;
        font-size: 0.8125rem;
        font-weight: 600;
        color: #666;
        margin-bottom: 0.25rem;
    }
    .portal-form-input {
        width: 100%;
        padding: 0.625rem 0.75rem;
        font-size: 0.9375rem;
        border: 2px solid #ddd;
        border-radius: 0.5rem;
        background-color: white;
        color: #333;
    }
    .portal-form-input:focus {
        outline: none;
        border-color: #80081C;
    }
    .portal-form-row {
        display: grid;
        grid-template-columns: 1fr 2fr;
        gap: 0.75rem;
    }
    .portal-phone-card {
        background: white;
        border-radius: 0.75rem;
        padding: 1rem 1.25rem;
        margin-bottom: 0.625rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        position: relative;
    }
    .portal-phone-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 0.75rem;
    }
    .portal-phone-title {
        font-size: 0.8125rem;
        font-weight: 600;
        color: #666;
    }
    .portal-phone-delete {
        background: none;
        border: none;
        color: #991b1b;
        cursor: pointer;
        padding: 0.25rem;
        display: flex;
        transition: opacity 0.2s;
    }
    .portal-phone-delete:hover { opacity: 0.7; }
    .portal-phone-delete svg { width: 1.125rem; height: 1.125rem; }
    .portal-phone-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.75rem;
    }
    .portal-phone-wa {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-top: 0.625rem;
        font-size: 0.8125rem;
        color: #666;
    }
    .portal-phone-wa input { width: 1rem; height: 1rem; accent-color: #80081C; }
    .portal-add-phone {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        width: 100%;
        padding: 0.625rem;
        margin-bottom: 1rem;
        background: white;
        color: #80081C;
        font-weight: 600;
        font-size: 0.875rem;
        border: 2px dashed #80081C;
        border-radius: 0.75rem;
        cursor: pointer;
        transition: background-color 0.2s;
    }
    .portal-add-phone:hover { background-color: #f5f1e9; }
    .portal-submit-btn {
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
        cursor: pointer;
        transition: background-color 0.2s;
    }
    .portal-submit-btn:hover {
        background-color: #660614;
    }
    .portal-validation-error {
        color: #dc2626;
        font-size: 0.8125rem;
        margin-top: 0.25rem;
    }
@endsection

@section('header')
    <header class="portal-header">
        <span class="portal-brand">Modifier</span>
        <a href="{{ route('portail.adhesion') }}" class="portal-header-action">Annuler</a>
    </header>
@endsection

@section('content')
    <form method="POST" action="{{ route('portail.adhesion.update') }}">
        @csrf
        <h1 style="font-size: 1.125rem; font-weight: 700; color: #333; margin-bottom: 1rem;">Demande de modification</h1>

        <div class="portal-form-card">
            <div class="portal-form-group">
                <label class="portal-form-label">Prénom</label>
                <input type="text" name="first_name" value="{{ old('first_name', $member->first_name) }}" class="portal-form-input" required>
                @error('first_name') <p class="portal-validation-error">{{ $message }}</p> @enderror
            </div>

            <div class="portal-form-group">
                <label class="portal-form-label">Nom</label>
                <input type="text" name="last_name" value="{{ old('last_name', $member->last_name) }}" class="portal-form-input" required>
                @error('last_name') <p class="portal-validation-error">{{ $message }}</p> @enderror
            </div>

            <div class="portal-form-group">
                <label class="portal-form-label">E-mail</label>
                <input type="email" name="email" value="{{ old('email', $member->email) }}" class="portal-form-input" required>
                @error('email') <p class="portal-validation-error">{{ $message }}</p> @enderror
            </div>

            <div class="portal-form-group">
                <label class="portal-form-label">Adresse</label>
                <input type="text" name="address" value="{{ old('address', $member->address) }}" class="portal-form-input">
                @error('address') <p class="portal-validation-error">{{ $message }}</p> @enderror
            </div>

            <div class="portal-form-row">
                <div class="portal-form-group">
                    <label class="portal-form-label">NPA</label>
                    <input type="text" name="postal_code" value="{{ old('postal_code', $member->postal_code) }}" class="portal-form-input">
                    @error('postal_code') <p class="portal-validation-error">{{ $message }}</p> @enderror
                </div>

                <div class="portal-form-group">
                    <label class="portal-form-label">Ville</label>
                    <input type="text" name="city" value="{{ old('city', $member->city) }}" class="portal-form-input">
                    @error('city') <p class="portal-validation-error">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <div id="phones-container">
            @foreach($member->phones as $i => $phone)
                <div class="portal-phone-card" data-phone>
                    <div class="portal-phone-header">
                        <span class="portal-phone-title">Téléphone</span>
                        <button type="button" class="portal-phone-delete" onclick="this.closest('[data-phone]').remove()" aria-label="Supprimer">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>
                    <div class="portal-phone-row">
                        <div class="portal-form-group">
                            <label class="portal-form-label">Libellé</label>
                            <select name="phones[{{ $i }}][label]" class="portal-form-input">
                                @foreach(\App\Enums\PhoneLabel::cases() as $pl)
                                    <option value="{{ $pl->value }}" {{ ($phone->label ?? 'Mobile') === $pl->value ? 'selected' : '' }}>{{ $pl->value }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="portal-form-group">
                            <label class="portal-form-label">Numéro</label>
                            <input type="tel" name="phones[{{ $i }}][number]" value="{{ $phone->phone_number }}" class="portal-form-input" required>
                        </div>
                    </div>
                    <label class="portal-phone-wa">
                        <input type="checkbox" name="phones[{{ $i }}][whatsapp]" value="1" {{ $phone->is_whatsapp ? 'checked' : '' }} onchange="onlyOneWhatsApp(this)">
                        WhatsApp
                    </label>
                </div>
            @endforeach
        </div>

        <button type="button" class="portal-add-phone" onclick="addPhone()">+ Ajouter un téléphone</button>

        <button type="submit" class="portal-submit-btn">
            <svg style="width:1.125rem;height:1.125rem;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            Envoyer ces modifications au comité
        </button>
    </form>
@endsection

@section('scripts')
<script>
function onlyOneWhatsApp(el) {
    if (el.checked) {
        document.querySelectorAll('#phones-container input[type="checkbox"]').forEach(function(cb) {
            if (cb !== el) cb.checked = false;
        });
    }
}
var phoneIndex = {{ $member->phones->count() }};
function addPhone() {
    var html = '<div class="portal-phone-card" data-phone>'
        + '<div class="portal-phone-header">'
        + '<span class="portal-phone-title">Téléphone</span>'
        + '<button type="button" class="portal-phone-delete" onclick="this.closest(\'[data-phone]\').remove()" aria-label="Supprimer">'
        + '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>'
        + '</button></div>'
        + '<div class="portal-phone-row">'
        + '<div class="portal-form-group"><label class="portal-form-label">Libellé</label>'
        + '<select name="phones[' + phoneIndex + '][label]" class="portal-form-input">'
        + '<option value="Mobile principal">Mobile principal</option>'
        + '<option value="Mobile secondaire">Mobile secondaire</option>'
        + '<option value="Maison">Maison</option>'
        + '<option value="Travail">Travail</option>'
        + '<option value="Autre">Autre</option>'
        + '</select></div>'
        + '<div class="portal-form-group"><label class="portal-form-label">Numéro</label>'
        + '<input type="tel" name="phones[' + phoneIndex + '][number]" class="portal-form-input" required></div>'
        + '</div>'
        + '<label class="portal-phone-wa"><input type="checkbox" name="phones[' + phoneIndex + '][whatsapp]" value="1" onchange="onlyOneWhatsApp(this)"> WhatsApp</label>'
        + '</div>';
    document.getElementById('phones-container').insertAdjacentHTML('beforeend', html);
    phoneIndex++;
}
</script>
@endsection
