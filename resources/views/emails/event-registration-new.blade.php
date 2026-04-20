@extends('emails.layout')

@section('content')
    <p style="margin-bottom: 16px;">Bonjour,</p>

    <p style="margin-bottom: 16px;">Tu souhaites t'inscrire à l'événement suivant :</p>

    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom: 20px; background-color: {{ config('association.colors.background') }}; border-radius: 6px; padding: 16px;">
        <tr>
            <td style="padding: 16px;">
                <strong style="font-size: 16px;">{{ $event->title }}</strong><br>
                📅 {{ $event->starts_at->format('d.m.Y à H:i') }}<br>
                @if($event->location)📍 {{ $event->location }}<br>@endif
                @if((float) $price > 0)💰 CHF {{ number_format((float) $price, 2, '.', '') }}@endif
            </td>
        </tr>
    </table>

    <p style="margin-bottom: 16px;">Pour finaliser ton inscription, complète tes informations :</p>

    <p style="text-align: center; margin-bottom: 20px;">
        <a href="{{ $registrationUrl }}" style="display: inline-block; background-color: {{ config('association.colors.primary') }}; color: #ffffff; font-weight: 600; font-size: 15px; padding: 12px 32px; border-radius: 6px; text-decoration: none;">
            M'inscrire
        </a>
    </p>

    <p style="margin-bottom: 16px; font-size: 13px; color: #666;">
        Ce lien expire dans 24 heures. Si tu n'as pas fait cette demande, ignore cet e-mail.
    </p>
@endsection
