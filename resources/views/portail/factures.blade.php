@extends('portail.layout')

@section('title', 'Mes factures')

@section('styles')
    .portal-invoice-card {
        background: white;
        border-radius: 0.5rem;
        padding: 1rem 1.25rem;
        margin-bottom: 0.625rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    }
    .portal-invoice-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }
    .portal-invoice-number {
        font-size: 0.9375rem;
        font-weight: 600;
        color: #333;
    }
    .portal-invoice-amount {
        font-size: 1rem;
        font-weight: 700;
        color: #333;
    }
    .portal-invoice-meta {
        font-size: 0.8125rem;
        color: #666;
        margin-top: 0.25rem;
    }
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
    .portal-badge-blue { background-color: #dbeafe; color: #1e40af; }
    .portal-badge-red { background-color: #fef2f2; color: #991b1b; }
    .portal-invoice-footer {
        display: flex;
        justify-content: flex-end;
        margin-top: 0.5rem;
    }
    .portal-pdf-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        font-size: 0.8125rem;
        font-weight: 500;
        color: #80081C;
        text-decoration: none;
        padding: 0.25rem 0.625rem;
        border-radius: 0.375rem;
        border: 1px solid #80081C;
        transition: background-color 0.2s;
    }
    .portal-pdf-btn:hover {
        background-color: #80081C;
        color: white;
    }
    .portal-pdf-btn svg {
        width: 1rem;
        height: 1rem;
    }
    .portal-empty {
        text-align: center;
        padding: 2rem 1rem;
        color: #999;
        font-size: 0.9375rem;
    }
@endsection

@section('header')
    <header class="portal-header">
        <span class="portal-brand">Mes factures</span>
        <a href="{{ route('portail.dashboard') }}" class="portal-header-action">Retour</a>
    </header>
@endsection

@section('content')
    @forelse($invoices as $invoice)
        <div class="portal-invoice-card">
            <div class="portal-invoice-header">
                <div>
                    <div class="portal-invoice-number">{{ $invoice->invoice_number }}</div>
                    <div class="portal-invoice-meta">
                        {{ $invoice->type->getLabel() }}
                        · <span class="portal-badge {{ match($invoice->statuscode->value) { 'P' => 'portal-badge-green', 'N' => 'portal-badge-orange', 'E' => 'portal-badge-blue', 'X' => 'portal-badge-red', default => '' } }}">{{ $invoice->statuscode->getLabel() }}</span>
                    </div>
                </div>
                <div class="portal-invoice-amount">CHF {{ number_format($invoice->amount, 2, '.', '') }}</div>
            </div>
            @if($invoice->lines->isNotEmpty())
                <div class="portal-invoice-meta" style="margin-top: 0.5rem;">
                    @foreach($invoice->lines as $line)
                        {{ $line->description }}@if(!$loop->last), @endif
                    @endforeach
                </div>
            @endif
            <div class="portal-invoice-footer">
                <a href="{{ route('portail.facture.pdf', $invoice) }}" class="portal-pdf-btn">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    PDF
                </a>
            </div>
        </div>
    @empty
        <div class="portal-empty">Aucune facture.</div>
    @endforelse
@endsection
