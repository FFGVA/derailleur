<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        @page {
            size: A4;
            margin: 0;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #333;
            margin: 0;
            padding: 0;
            width: 210mm;
            min-height: 297mm;
            position: relative;
        }
        .page-content {
            padding: 20mm 20mm 0 20mm;
        }
        .logo {
            margin-bottom: 15px;
        }
        .logo img {
            height: 50px;
        }
        .creditor {
            font-size: 10px;
            color: #666;
            margin-bottom: 30px;
            border-bottom: 2px solid #80081C;
            padding-bottom: 12px;
        }
        .invoice-title {
            font-size: 20px;
            font-weight: bold;
            color: #80081C;
            margin: 25px 0 20px;
        }
        table.details {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table.details td {
            padding: 4px 0;
            vertical-align: top;
        }
        table.details td.label {
            font-weight: bold;
            width: 140px;
            color: #555;
        }
        table.items {
            width: 100%;
            border-collapse: collapse;
            margin: 25px 0 15px;
        }
        table.items th {
            background-color: #80081C;
            color: #fff;
            text-align: left;
            padding: 8px 10px;
            font-size: 11px;
        }
        table.items td {
            padding: 8px 10px;
            border-bottom: 1px solid #ddd;
        }
        table.items td.amount,
        table.items th.amount {
            text-align: right;
        }
        .total {
            text-align: right;
            font-size: 14px;
            font-weight: bold;
            margin: 10px 0 0;
            padding-right: 10px;
        }
        .qr-section {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
        }
        /* Override QR bill styles for DomPDF compatibility */
        .qr-section table {
            width: 100% !important;
        }
        .qr-section * {
            font-family: Arial, Helvetica, sans-serif !important;
        }
        .qr-section h2 {
            font-size: 9px !important;
            font-weight: bold !important;
        }
        .qr-section p {
            font-size: 10px !important;
        }
        #qr-bill-receipt {
            font-size: 9px !important;
        }
        #qr-bill-payment-part {
            font-size: 10px !important;
        }
    </style>
</head>
<body>
    <div class="page-content">
        <div class="logo">
            <img src="{{ public_path('images/logo-ffgva.png') }}" alt="FFGVA">
        </div>

        <div class="creditor">
            {{ config('ffgva.creditor_name') }}<br>
            c/o Livia Wagner<br>
            {{ config('ffgva.creditor_address') }}<br>
            {{ config('ffgva.creditor_postal_code') }} {{ config('ffgva.creditor_city') }}
        </div>

        <h2 class="invoice-title">Facture — Cotisation annuelle</h2>

        <table class="details">
            <tr>
                <td class="label">Membre :</td>
                <td>{{ $member->first_name }} {{ $member->last_name }}</td>
            </tr>
            @if($member->address || $member->city)
            <tr>
                <td class="label">Adresse :</td>
                <td>
                    @if($member->address){{ $member->address }}<br>@endif
                    @if($member->postal_code){{ $member->postal_code }} @endif{{ $member->city ?? '' }}
                </td>
            </tr>
            @endif
            <tr>
                <td class="label">Date :</td>
                <td>{{ $date }}</td>
            </tr>
            <tr>
                <td class="label">Référence :</td>
                <td>{{ $reference }}</td>
            </tr>
        </table>

        <table class="items">
            <thead>
                <tr>
                    <th>Description</th>
                    <th class="amount">Montant</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Cotisation annuelle Fast and Female Geneva {{ date('Y') }}</td>
                    <td class="amount">CHF {{ $amount }}</td>
                </tr>
            </tbody>
        </table>

        <div class="total">Total : CHF {{ $amount }}</div>
    </div>

    <div class="qr-section">
        {!! $qrHtml !!}
    </div>
</body>
</html>
