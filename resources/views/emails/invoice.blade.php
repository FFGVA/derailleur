@extends('emails.layout')

@section('content')
    <p style="font-size: 16px; line-height: 1.6; margin: 0 0 16px;">
        Chère {{ $member->first_name }},
    </p>

    <p style="font-size: 15px; line-height: 1.6; margin: 0 0 20px;">
        Tu trouveras ci-joint ta facture <strong>{{ $invoice->invoice_number }}</strong>.
    </p>

    {{-- Invoice lines table --}}
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom: 16px;">
        <tr>
            <td style="padding: 8px 10px; background-color: #80081C; color: #ffffff; font-weight: bold; font-size: 13px;">Description</td>
            <td style="padding: 8px 10px; background-color: #80081C; color: #ffffff; font-weight: bold; font-size: 13px; text-align: right; width: 100px;">Montant</td>
        </tr>
        @foreach($lines as $line)
        <tr>
            <td style="padding: 8px 10px; border-bottom: 1px solid #e5e5e5; font-size: 14px;">{{ $line->description }}</td>
            <td style="padding: 8px 10px; border-bottom: 1px solid #e5e5e5; font-size: 14px; text-align: right; white-space: nowrap;">CHF {{ number_format($line->amount, 2, '.', '') }}</td>
        </tr>
        @endforeach
        <tr>
            <td style="padding: 10px 10px; font-weight: bold; font-size: 15px;">Total</td>
            <td style="padding: 10px 10px; font-weight: bold; font-size: 15px; text-align: right; white-space: nowrap;">CHF {{ number_format($invoice->amount, 2, '.', '') }}</td>
        </tr>
    </table>

    {{-- QR code for payment --}}
    @if($qrImageBase64)
    <div style="text-align: center; margin: 24px 0;">
        <p style="font-size: 13px; color: #666; margin-bottom: 8px;">Scanne le QR code pour payer :</p>
        <img src="{{ $qrImageBase64 }}" alt="QR Code paiement" style="width: 180px; height: 180px;"><br>
        @php
            $iban = config('ffgva.iban');
            $formatted = 'IBAN: ' . implode(' ', str_split($iban, 4));
        @endphp
        <p style="font-size: 13px; color: #333; margin-top: 8px; font-family: monospace; letter-spacing: 1px;">{{ $formatted }}</p>
    </div>
    @endif

    @if($invoice->getRawOriginal('type') === 'E' && $invoice->events->first())
        <p style="text-align: center; margin: 20px 0;">
            <a href="{{ route('portail.evenement', $invoice->events->first()) }}" style="display: inline-block; background-color: #80081C; color: #ffffff; font-weight: 600; font-size: 15px; padding: 12px 32px; border-radius: 6px; text-decoration: none;">
                Voir mon inscription sur le portail
            </a>
        </p>
    @endif

    <p style="font-size: 14px; line-height: 1.6; margin: 20px 0 0; color: #666;">
        Le bulletin de versement complet se trouve en pièce jointe.
    </p>
@endsection
