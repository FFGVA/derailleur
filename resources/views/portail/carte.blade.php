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
        font-size: 1rem;
        font-weight: 600;
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
@endsection

@section('header')
    <header class="portal-header">
        <span class="portal-brand">Ma carte</span>
        <a href="{{ route('portail.dashboard') }}" class="portal-header-action">Retour</a>
    </header>
@endsection

@section('content')
    <div class="carte-card">
        <div class="carte-name">{{ $member->first_name }} {{ $member->last_name }}</div>
        @if($isActive)
            <div class="carte-status carte-status-active">Membre active</div>
            @if($member->membership_end)
                <div class="carte-date">Valide jusqu'au {{ $member->membership_end->format('d.m.Y') }}</div>
            @endif
        @else
            <div class="carte-status carte-status-inactive">Adhésion inactive</div>
        @endif
    </div>

    <div class="carte-card">
        <div class="carte-qr" id="qrcode"></div>
        <div class="carte-hint">Le QR code se renouvelle automatiquement</div>
    </div>

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

        // Refresh QR URL every 5 minutes
        setInterval(function() {
            fetch('{{ route("portail.carte.qr-url") }}', {
                credentials: 'same-origin'
            })
            .then(r => r.json())
            .then(data => {
                currentUrl = data.url;
                renderQr(currentUrl);
            });
        }, 5 * 60 * 1000);
    </script>
@endsection
