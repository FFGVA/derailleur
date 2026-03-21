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
    .portal-detail-title-row {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.75rem;
    }
    .portal-detail-title {
        font-size: 1.125rem;
        font-weight: 700;
        color: #333;
        flex: 1;
    }
    .portal-desc-btn {
        flex-shrink: 0;
        background: none;
        border: 1px solid #ddd;
        border-radius: 0.375rem;
        padding: 0.375rem;
        cursor: pointer;
        color: #666;
        transition: background-color 0.2s;
    }
    .portal-desc-btn:hover {
        background-color: #f5f1e9;
    }
    .portal-desc-btn svg {
        width: 1.125rem;
        height: 1.125rem;
        display: block;
    }
    .portal-info-row {
        display: flex;
        justify-content: space-between;
        padding: 0.5rem 0;
        border-bottom: 1px solid #f0ede8;
        font-size: 0.9375rem;
    }
    .portal-info-row:last-child { border-bottom: none; }
    .portal-info-label { color: #666; }
    .portal-info-value { font-weight: 500; color: #333; text-align: right; }
    .portal-info-value a { color: #80081C; text-decoration: none; }
    .portal-info-value a:hover { text-decoration: underline; }
    .portal-section-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 0.5rem;
    }
    .portal-section-title {
        font-size: 1rem;
        font-weight: 700;
        color: #333;
    }
    .portal-add-btn {
        width: 1.75rem;
        height: 1.75rem;
        border: 2px solid #80081C;
        border-radius: 0.375rem;
        background: white;
        color: #80081C;
        font-size: 1.125rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.15s;
        line-height: 1;
    }
    .portal-add-btn:hover {
        background-color: #80081C;
        color: white;
    }
    .portal-popup-select {
        width: 100%;
        padding: 0.75rem 1rem;
        font-size: 0.9375rem;
        border: 2px solid #ddd;
        border-radius: 0.5rem;
        background-color: white;
        color: #333;
        margin-bottom: 1rem;
        appearance: none;
        -webkit-appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%23666' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 1rem center;
    }
    .portal-popup-select:focus {
        outline: none;
        border-color: #80081C;
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
    .portal-participant {
        display: flex;
        align-items: center;
        gap: 0.625rem;
        padding: 0.75rem 0;
        border-bottom: 1px solid #f0ede8;
    }
    .portal-participant:last-child { border-bottom: none; }
    .portal-participant-info {
        flex: 1;
        min-width: 0;
    }
    .portal-participant-name {
        font-size: 0.9375rem;
        font-weight: 500;
        color: #333;
    }
    .portal-participant-name a {
        color: #333;
        text-decoration: none;
    }
    .portal-no-photo {
        flex-shrink: 0;
        width: 1.25rem;
        color: #991b1b;
    }
    .portal-no-photo svg {
        width: 1.125rem;
        height: 1.125rem;
    }
    .portal-photo-spacer {
        flex-shrink: 0;
        width: 1.25rem;
    }
    .portal-participant-status {
        flex-shrink: 0;
        width: 4.5rem;
        text-align: center;
    }
    .portal-status-badge {
        display: inline-block;
        font-size: 0.625rem;
        font-weight: 600;
        padding: 0.0625rem 0.375rem;
        border-radius: 0.1875rem;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        white-space: nowrap;
    }
    .portal-badge-green { background-color: #dcfce7; color: #166534; }
    .portal-badge-orange { background-color: #fff7ed; color: #9a3412; }
    .portal-badge-red { background-color: #fef2f2; color: #991b1b; }
    .portal-participant-icons {
        display: flex;
        gap: 1.25rem;
        align-items: center;
    }
    .portal-participant-icons a {
        color: #666;
        text-decoration: none;
        display: flex;
    }
    .portal-participant-icons a:hover { color: #80081C; }
    .portal-participant-icons svg {
        width: 1.25rem;
        height: 1.25rem;
    }
    .portal-presence-btn {
        flex-shrink: 0;
        width: 1.25rem;
        height: 1.25rem;
        border-radius: 50%;
        border: 2px solid #ddd;
        background: white;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 0.625rem;
        font-weight: 700;
        padding: 0;
        transition: all 0.15s;
    }
    .portal-presence-true {
        background-color: #dcfce7;
        border-color: #22c55e;
        color: #166534;
    }
    .portal-presence-false {
        background-color: #fef2f2;
        border-color: #ef4444;
        color: #991b1b;
    }
    .portal-presence-null {
        background-color: #f5f5f4;
        border-color: #d6d3d1;
        color: #a8a29e;
    }
    /* Description popup */
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
        max-height: 80vh;
        overflow-y: auto;
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
        white-space: pre-wrap;
    }
    .portal-popup-close {
        margin-top: 1rem;
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
    .portal-empty {
        text-align: center;
        padding: 1.5rem 1rem;
        color: #999;
        font-size: 0.9375rem;
    }
@endsection

@section('header')
    <header class="portal-header">
        <span class="portal-brand">Événement</span>
        <a href="{{ route('portail.peloton') }}" class="portal-header-action">Retour</a>
    </header>
@endsection

@section('content')
    {{-- Event detail card --}}
    <div class="portal-detail-card">
        <div class="portal-detail-title-row">
            <span class="portal-detail-title">{{ $event->title }}</span>
            @if($participants->contains(fn ($p) => !$p->photo_ok))
                <span class="portal-desc-btn" style="color: #991b1b; cursor: default; border-color: #991b1b;" title="Pas de photo pour certaines participantes">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </span>
            @endif
            @if($event->description)
                <button class="portal-desc-btn" onclick="document.getElementById('descPopup').classList.add('active')" aria-label="Description">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </button>
            @endif
        </div>
        <div class="portal-info-row">
            <span class="portal-info-label">Début</span>
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
                <span class="portal-info-label">Lieu</span>
                <span class="portal-info-value">
                    <a href="https://maps.google.com/?q={{ urlencode($event->location) }}" target="_blank" rel="noopener">{{ $event->location }}</a>
                </span>
            </div>
        @endif
    </div>

    {{-- Participants card --}}
    <div class="portal-detail-card">
        <div class="portal-section-header">
            <div class="portal-section-title">Présences ({{ $participants->count() }})</div>
            @if($availableMembers->isNotEmpty())
                <button class="portal-add-btn" onclick="document.getElementById('addPopup').classList.add('active')" aria-label="Ajouter">+</button>
            @endif
        </div>

        @forelse($participants as $participant)
            @php
                $phone = $participant->phones->first();
                $whatsappPhone = $participant->phones->firstWhere('is_whatsapp', true);
                $rawPresent = $participant->pivot->getRawOriginal('present');
            @endphp
            <div class="portal-participant">
                <div class="portal-participant-info">
                    <div class="portal-participant-name">
                        <a href="mailto:{{ $participant->email }}">{{ $participant->first_name }} {{ $participant->last_name }}</a>
                    </div>
                </div>
                @if(!$participant->photo_ok)
                    <div class="portal-no-photo" title="Pas de photo">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                @else
                    <div class="portal-photo-spacer"></div>
                @endif
                <div class="portal-participant-status">
                    <span class="portal-status-badge {{ match($participant->pivot->status->value) { 'C' => 'portal-badge-green', 'N' => 'portal-badge-orange', 'X' => 'portal-badge-red', default => '' } }}">{{ $participant->pivot->status->getLabel() }}</span>
                </div>
                <div class="portal-participant-icons">
                    @if($phone)
                        <a href="tel:{{ $phone->phone_number }}" aria-label="Appeler">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                        </a>
                    @endif
                    @if($whatsappPhone)
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $whatsappPhone->phone_number) }}" target="_blank" rel="noopener" aria-label="WhatsApp">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                        </a>
                    @endif
                    <form method="POST" action="{{ route('portail.peloton.presence', [$event, $participant]) }}" style="display:flex;">
                        @csrf
                        <button type="submit" class="portal-presence-btn portal-presence-{{ $rawPresent === null ? 'null' : ($rawPresent ? 'true' : 'false') }}">
                            @if($rawPresent === null)
                                —
                            @elseif($rawPresent)
                                ✓
                            @else
                                ✗
                            @endif
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="portal-empty">Aucun participant.</div>
        @endforelse
    </div>

    {{-- Add participant popup --}}
    @if($availableMembers->isNotEmpty())
        <div id="addPopup" class="portal-overlay" onclick="if(event.target===this)this.classList.remove('active')">
            <div class="portal-popup">
                <div class="portal-popup-title">Ajouter une participante</div>
                <form method="POST" action="{{ route('portail.peloton.add', $event) }}">
                    @csrf
                    <select name="member_id" class="portal-popup-select" required>
                        <option value="">Choisir...</option>
                        @foreach($availableMembers as $m)
                            <option value="{{ $m->id }}">{{ $m->first_name }} {{ $m->last_name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="portal-popup-submit">Ajouter</button>
                </form>
                <button class="portal-popup-close" onclick="document.getElementById('addPopup').classList.remove('active')">Annuler</button>
            </div>
        </div>
    @endif

    {{-- Description popup --}}
    @if($event->description)
        <div id="descPopup" class="portal-overlay" onclick="if(event.target===this)this.classList.remove('active')">
            <div class="portal-popup">
                <div class="portal-popup-title">Description</div>
                <div class="portal-popup-body">{{ $event->description }}</div>
                <button class="portal-popup-close" onclick="document.getElementById('descPopup').classList.remove('active')">Fermer</button>
            </div>
        </div>
    @endif
@endsection
