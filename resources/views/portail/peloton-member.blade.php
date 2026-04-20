@extends('portail.layout')

@section('title', $target->first_name . ' ' . $target->last_name)

@section('styles')
    .portal-detail-name {
        font-size: 1.125rem;
        font-weight: 700;
        color: #333;
        margin-bottom: 0.75rem;
    }
    .portal-info-row {
        display: flex;
        justify-content: space-between;
        padding: 0.5rem 0;
        border-bottom: 1px solid #f0ede8;
        font-size: 0.9375rem;
    }
    .portal-info-label { color: #666; }
    .portal-info-value { font-weight: 500; color: #333; text-align: right; }
    .portal-info-value a { color: var(--color-primary); text-decoration: none; }
    .portal-info-value a:hover { text-decoration: underline; }
    .portal-no-photo-banner {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.625rem 1rem;
        background-color: #fef2f2;
        color: #991b1b;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        font-weight: 500;
        margin-bottom: 1rem;
    }
    .portal-no-photo-banner svg { width: 1.125rem; height: 1.125rem; flex-shrink: 0; }
@endsection

@section('header')
    <header class="portal-header">
        <span class="portal-brand">Membre</span>
        <a href="{{ route('portail.peloton.event', $event) }}" class="portal-header-action">Retour</a>
    </header>
@endsection

@section('content')
    @if(!$target->photo_ok)
        <div class="portal-no-photo-banner">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Pas de photo
        </div>
    @endif

    <div class="portal-detail-card">
        <div class="portal-detail-name">{{ $target->first_name }} {{ $target->last_name }}</div>

        <div class="portal-info-row">
            <span class="portal-info-label">E-mail</span>
            <span class="portal-info-value">
                <a href="mailto:{{ $target->email }}">{{ $target->email }}</a>
            </span>
        </div>

        @php $sortedPhones = \App\Enums\PhoneLabel::sortPhones($target->phones); @endphp
        @foreach($sortedPhones as $phone)
            <div class="portal-info-row">
                <span class="portal-info-label">{{ $phone->label ?? 'Téléphone' }}</span>
                <span class="portal-info-value">
                    @if($phone->is_whatsapp)
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $phone->phone_number) }}" target="_blank" rel="noopener" style="color: #999; display: inline-flex; vertical-align: middle; margin-right: 0.25rem;"><svg style="width: 0.875rem; height: 0.875rem;" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg></a>
                    @endif
                    <a href="tel:{{ $phone->phone_number }}">{{ $phone->phone_number }}</a>
                </span>
            </div>
        @endforeach
    </div>
@endsection
