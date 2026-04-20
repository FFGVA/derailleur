@extends('emails.layout')

@section('content')
    <p style="margin-bottom: 16px;">Bonjour {{ $member->first_name }},</p>

    <p style="margin-bottom: 16px;">Tu souhaites t'inscrire à l'événement suivant :</p>

    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom: 20px; background-color: {{ config('association.colors.background') }}; border-radius: 6px; padding: 16px;">
        <tr>
            <td style="padding: 16px;">
                <strong style="font-size: 16px;">{{ $event->title }}</strong><br>
                📅 {{ $event->starts_at->format('d.m.Y à H:i') }}<br>
                @if($event->location)📍 {{ $event->location }}<br>@endif
                @if((float) $applicablePrice > 0)💰 CHF {{ number_format((float) $applicablePrice, 2, '.', '') }}@endif
            </td>
        </tr>
    </table>

    <p style="text-align: center; margin-bottom: 20px;">
        <a href="{{ $confirmUrl }}" style="display: inline-block; background-color: {{ config('association.colors.primary') }}; color: #ffffff; font-weight: 600; font-size: 15px; padding: 12px 32px; border-radius: 6px; text-decoration: none;">
            Confirmer mon inscription
        </a>
    </p>

    @if((float) $applicablePrice > 0)
        <p style="margin-bottom: 16px; font-size: 13px; color: #666;">
            Une facture de CHF {{ number_format((float) $applicablePrice, 2, '.', '') }} te sera envoyée par e-mail après confirmation.
        </p>
    @endif

    <p style="margin-bottom: 16px; font-size: 13px; color: #666;">
        Ce lien expire le {{ $expiresAt }}. Si tu n'as pas fait cette demande, ignore cet e-mail.
    </p>
@endsection
