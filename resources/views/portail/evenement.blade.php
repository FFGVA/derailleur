@extends('portail.layout')

@section('title', $event->title)

@section('styles')
    .portal-detail-card {
        background: white;
        border-radius: 0.75rem;
        padding: 1.25rem;
        margin-bottom: 1rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    }
    .portal-detail-title {
        font-size: 1.125rem;
        font-weight: 700;
        color: #333;
        margin-bottom: 0.5rem;
    }
    .portal-detail-description {
        font-size: 0.9375rem;
        line-height: 1.6;
        color: #555;
        white-space: pre-wrap;
        margin-bottom: 0.75rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid #f0ede8;
    }
    .portal-info-row {
        display: flex;
        justify-content: space-between;
        padding: 0.5rem 0;
        border-bottom: 1px solid #f0ede8;
        font-size: 0.9375rem;
    }
    .portal-info-row:last-child { border-bottom: none; }
    .portal-info-label { color: #666; display: flex; align-items: center; gap: 0.375rem; }
    .portal-info-value { font-weight: 500; color: #333; text-align: right; }
    .portal-info-value a { color: #80081C; text-decoration: none; }
    .portal-info-value a:hover { text-decoration: underline; }
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
    .portal-chef-card {
        background: white;
        border-radius: 0.75rem;
        padding: 1rem 1.25rem;
        margin-bottom: 1rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .portal-chef-info {
        flex: 1;
        min-width: 0;
    }
    .portal-chef-label {
        font-size: 0.6875rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        color: #999;
        margin-bottom: 0.125rem;
    }
    .portal-chef-name {
        font-size: 0.9375rem;
        font-weight: 600;
        color: #333;
    }
    .portal-chef-email {
        font-size: 0.8125rem;
        color: #80081C;
        text-decoration: none;
    }
    .portal-chef-email:hover { text-decoration: underline; }
    .portal-chef-icons {
        display: flex;
        gap: 1.25rem;
        align-items: center;
        flex-shrink: 0;
    }
    .portal-chef-icons a {
        color: #666;
        text-decoration: none;
        display: flex;
    }
    .portal-chef-icons a:hover { color: #80081C; }
    .portal-chef-icons svg {
        width: 1.25rem;
        height: 1.25rem;
    }
    .portal-register-btn {
        display: block;
        width: 100%;
        padding: 0.875rem;
        background-color: #80081C;
        color: white;
        font-weight: 600;
        font-size: 1rem;
        border: none;
        border-radius: 0.5rem;
        cursor: pointer;
        text-align: center;
        transition: background-color 0.2s;
        margin-top: 1rem;
    }
    .portal-register-btn:hover {
        background-color: #660614;
    }
    .portal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.4);
        z-index: 50;
        align-items: center;
        justify-content: center;
        padding: 1.25rem;
    }
    .portal-overlay.active { display: flex; }
    .portal-popup {
        background: white;
        border-radius: 0.75rem;
        padding: 1.5rem;
        max-width: 480px;
        width: 100%;
        box-shadow: 0 4px 24px rgba(0,0,0,0.15);
    }
    .portal-popup-title {
        font-size: 1rem;
        font-weight: 700;
        margin-bottom: 0.75rem;
        color: #333;
    }
    .portal-popup-body {
        font-size: 0.9375rem;
        line-height: 1.6;
        color: #555;
        margin-bottom: 1rem;
    }
    .portal-popup-submit {
        width: 100%;
        padding: 0.75rem;
        background: #80081C;
        border: none;
        border-radius: 0.5rem;
        font-weight: 600;
        cursor: pointer;
        font-size: 0.9375rem;
        color: white;
        transition: background-color 0.2s;
    }
    .portal-popup-submit:hover {
        background-color: #660614;
    }
    .portal-popup-close {
        margin-top: 0.5rem;
        width: 100%;
        padding: 0.625rem;
        background: #f5f1e9;
        border: none;
        border-radius: 0.5rem;
        font-weight: 600;
        cursor: pointer;
        font-size: 0.9375rem;
        color: #333;
    }
    .portal-cancel-btn {
        display: block;
        width: 100%;
        padding: 0.75rem;
        background: white;
        color: #991b1b;
        font-weight: 600;
        font-size: 0.9375rem;
        border: 2px solid #991b1b;
        border-radius: 0.5rem;
        cursor: pointer;
        text-align: center;
        transition: all 0.2s;
        margin-top: 0.75rem;
    }
    .portal-cancel-btn:hover {
        background-color: #991b1b;
        color: white;
    }
    .portal-popup-cancel-submit {
        width: 100%;
        padding: 0.75rem;
        background: #991b1b;
        border: none;
        border-radius: 0.5rem;
        font-weight: 600;
        cursor: pointer;
        font-size: 0.9375rem;
        color: white;
        transition: background-color 0.2s;
    }
    .portal-popup-cancel-submit:hover {
        background-color: #7f1d1d;
    }
@endsection

@section('header')
    <header class="portal-header">
        <span class="portal-brand">Événement</span>
        <a href="{{ route('portail.dashboard') }}" class="portal-header-action">Retour</a>
    </header>
@endsection

@section('content')
    <div class="portal-detail-card">
        <div class="portal-detail-title">{{ $event->title }}</div>

        @if($event->description)
            <div class="portal-detail-description">{{ $event->description }}</div>
        @endif

        <div class="portal-info-row">
            <span class="portal-info-label">Début <a href="{{ route('portail.evenement.ical', $event) }}" title="Ajouter au calendrier" style="color: #80081C; display: inline-flex;"><svg width="1rem" height="1rem" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg></a></span>
            <span class="portal-info-value">{{ $event->starts_at->format('d.m.Y H:i') }}</span>
        </div>
        @if($event->ends_at)
            <div class="portal-info-row">
                <span class="portal-info-label">Fin</span>
                <span class="portal-info-value">{{ $event->ends_at->format('d.m.Y H:i') }}</span>
            </div>
        @endif
        @if($event->location)
            <div class="portal-info-row">
                <span class="portal-info-label">Lieu <a href="#" onclick="openMaps('{{ urlencode($event->location) }}');return false;" style="color: #80081C; display: inline-flex;" aria-label="Ouvrir dans Plans"><svg width="1rem" height="1rem" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg></a></span>
                <span class="portal-info-value">{{ $event->location }}</span>
            </div>
        @endif
        @if($event->gpx_file)
            <div class="portal-info-row">
                <span class="portal-info-label">Parcours <a href="{{ asset('storage/' . $event->gpx_file) }}" download="{{ Str::slug($event->title) }}-{{ $event->starts_at->format('Y-m-d') }}.gpx" style="color: #80081C; display: inline-flex;" title="Télécharger GPX"><svg width="1rem" height="1rem" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l5.447 2.724A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg></a></span>
                <span class="portal-info-value"><a href="{{ asset('storage/' . $event->gpx_file) }}" download="{{ Str::slug($event->title) }}-{{ $event->starts_at->format('Y-m-d') }}.gpx">Télécharger GPX</a></span>
            </div>
        @endif
        @if($registration)
            <div class="portal-info-row">
                <span class="portal-info-label">Statut</span>
                <span class="portal-info-value">
                    <span class="portal-badge {{ match($registration->status->value) { 'C' => 'portal-badge-green', 'N' => 'portal-badge-orange', 'X' => 'portal-badge-red', default => '' } }}">{{ $registration->status->getLabel() }}</span>
                </span>
            </div>
        @endif
    </div>

    @if($event->chefPeloton)
        @php
            $chef = $event->chefPeloton;
            $chefPhone = $chef->phones->first();
            $chefWhatsapp = $chef->phones->firstWhere('is_whatsapp', true);
        @endphp
        <div class="portal-chef-card">
            <div class="portal-chef-info">
                <div class="portal-chef-label">Cheffe de peloton</div>
                <div class="portal-chef-name">{{ $chef->first_name }} {{ $chef->last_name }}</div>
                <a href="mailto:{{ $chef->email }}" class="portal-chef-email">{{ $chef->email }}</a>
            </div>
            <div class="portal-chef-icons">
                @if($chefPhone)
                    <a href="tel:{{ $chefPhone->phone_number }}" aria-label="Appeler">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                    </a>
                @endif
                @if($chefWhatsapp)
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $chefWhatsapp->phone_number) }}" target="_blank" rel="noopener" aria-label="WhatsApp">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                    </a>
                @endif
            </div>
        </div>
    @endif

    @if(!$registration)
        @if($applicablePrice > 0)
            <button type="button" class="portal-register-btn" onclick="document.getElementById('confirmPopup').classList.add('active')">Je m'inscris</button>
        @else
            <form method="POST" action="{{ route('portail.evenement.inscrire', $event) }}">
                @csrf
                <button type="submit" class="portal-register-btn">Je m'inscris</button>
            </form>
        @endif
    @else
        <button type="button" class="portal-cancel-btn" onclick="document.getElementById('cancelPopup').classList.add('active')">Je ne peux pas venir</button>
    @endif

    @if($registration)
        <div id="cancelPopup" class="portal-overlay" onclick="if(event.target===this)this.classList.remove('active')">
            <div class="portal-popup">
                <div class="portal-popup-title">Annuler l'inscription</div>
                <div class="portal-popup-body">
                    Tu ne pourras plus participer à <strong>{{ $event->title }}</strong>. Confirmer l'annulation ?
                </div>
                <form method="POST" action="{{ route('portail.evenement.annuler', $event) }}">
                    @csrf
                    <button type="submit" class="portal-popup-cancel-submit">Confirmer l'annulation</button>
                </form>
                <button class="portal-popup-close" onclick="document.getElementById('cancelPopup').classList.remove('active')">Retour</button>
            </div>
        </div>
    @endif

    @if(!$registration && $applicablePrice > 0)
        <div id="confirmPopup" class="portal-overlay" onclick="if(event.target===this)this.classList.remove('active')">
            <div class="portal-popup">
                <div class="portal-popup-title">Confirmer l'inscription</div>
                <div class="portal-popup-body">
                    L'événement <strong>{{ $event->title }}</strong> coûte <strong>CHF {{ number_format($applicablePrice, 2, '.', '') }}</strong>. Une facture te sera envoyée par e-mail.
                </div>
                <form method="POST" action="{{ route('portail.evenement.inscrire', $event) }}">
                    @csrf
                    <button type="submit" class="portal-popup-submit">Confirmer</button>
                </form>
                <button class="portal-popup-close" onclick="document.getElementById('confirmPopup').classList.remove('active')">Annuler</button>
            </div>
        </div>
    @endif
@endsection
