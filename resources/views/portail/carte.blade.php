@extends('portail.layout')

@section('title', 'Ma carte de membre')

@section('styles')
    .carte-card {
        background: white;
        border-radius: 0.75rem;
        padding: 1.5rem;
        margin-bottom: 1rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        text-align: center;
    }
    .carte-name {
        font-size: 1.5rem;
        font-weight: 700;
        color: #333;
        margin-bottom: 0.5rem;
    }
    .carte-status {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
    }
    .carte-status-active {
        color: #166534;
    }
    .carte-status-inactive {
        color: #991b1b;
    }
    .carte-date {
        font-size: 0.875rem;
        color: #666;
    }
    .carte-qr {
        display: flex;
        justify-content: center;
        padding: 1rem 0;
    }
    .carte-hint {
        font-size: 0.75rem;
        color: #999;
        margin-top: 0.5rem;
    }
    .carte-download {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: #80081C;
        color: white;
        padding: 0.625rem 1.25rem;
        border-radius: 0.5rem;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.9375rem;
        transition: background 0.2s;
    }
    .carte-download:hover {
        background: #660616;
    }
@endsection

@section('header')
    <header class="portal-header">
        <span class="portal-brand">Ma carte</span>
        <a href="{{ route('portail.dashboard') }}" class="portal-header-action">Retour</a>
    </header>
@endsection

@section('content')
    <div class="carte-card">
        <img src="{{ asset('images/logo-ffgva.png') }}" alt="Fast and Female Geneva" style="max-width: 160px; margin-bottom: 1rem;">
        <div class="carte-name">{{ $member->first_name }} {{ $member->last_name }}</div>
        @if($member->member_number)
            <div style="font-size: 1.5rem; font-weight: 700; color: #333; margin-bottom: 0.5rem;">N° {{ $member->member_number }}</div>
        @endif
        @if($isActive)
            <div class="carte-status carte-status-active">Membre active</div>
            <div class="carte-date">
                @if($member->membership_end)
                    Valide jusqu'au {{ $member->membership_end->format('d.m.Y') }}
                @else
                    Adhésion en cours
                @endif
            </div>
        @else
            <div class="carte-status carte-status-inactive">Adhésion inactive</div>
        @endif
    </div>

    @if($isActive)
        <div class="carte-card">
            <div class="carte-qr" id="qrcode"></div>
            <div class="carte-hint">Le QR code se renouvelle automatiquement</div>
        </div>
    @endif

    @if($isActive)
        <div class="carte-card">
            <a href="{{ route('portail.carte.pdf') }}" class="carte-download">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" style="width:1.25rem;height:1.25rem;"><path d="M5 4h14a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2zm7.5 7V7h-1v4H8.25l3.25 3.25L14.75 11H11.5zM7 16h10v1H7v-1z"/></svg>
                T&eacute;l&eacute;charger carte
            </a>
        </div>
    @endif

    @if($isActive)
        <script src="https://cdn.jsdelivr.net/npm/qrcode-generator@1.4.4/qrcode.min.js"></script>
        <script>
            let currentUrl = @json($qrUrl);

            function renderQr(url) {
                const qr = qrcode(0, 'M');
                qr.addData(url);
                qr.make();
                document.getElementById('qrcode').innerHTML = qr.createSvgTag({ cellSize: 6, margin: 4 });
            }

            renderQr(currentUrl);

            setInterval(function() {
                fetch('{{ route("portail.carte.qr-url") }}', {
                    credentials: 'same-origin'
                })
                .then(r => r.json())
                .then(data => {
                    currentUrl = data.url;
                    renderQr(currentUrl);
                });
            }, 60 * 1000);
        </script>
    @endif
@endsection
