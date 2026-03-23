@extends('emails.layout')

@section('content')
    <p style="margin-bottom: 16px;">Bonjour {{ $member->first_name }},</p>

    <p style="margin-bottom: 16px;">Ton inscription à <strong>{{ $event->title }}</strong> est confirmée.</p>

    <table role="presentation" cellspacing="0" cellpadding="0" border="0" style="margin-bottom: 16px; font-size: 14px;">
        <tr>
            <td style="padding: 4px 16px 4px 0; color: #666;">Date</td>
            <td style="padding: 4px 0; font-weight: 500;">{{ $event->starts_at->format('d.m.Y H:i') }}</td>
        </tr>
        @if($event->ends_at)
            <tr>
                <td style="padding: 4px 16px 4px 0; color: #666;">Fin</td>
                <td style="padding: 4px 0; font-weight: 500;">{{ $event->ends_at->format('d.m.Y H:i') }}</td>
            </tr>
        @endif
        @if($event->location)
            <tr>
                <td style="padding: 4px 16px 4px 0; color: #666;">Lieu</td>
                <td style="padding: 4px 0; font-weight: 500;">{{ $event->location }}</td>
            </tr>
        @endif
    </table>

    <p style="text-align: center; margin-bottom: 20px;">
        <a href="{{ route('portail.evenement', $event) }}" style="display: inline-block; background-color: #80081C; color: #ffffff; font-weight: 600; font-size: 15px; padding: 12px 32px; border-radius: 6px; text-decoration: none;">
            Voir mon inscription sur le portail
        </a>
    </p>

    <p style="font-size: 13px; color: #666;">Tu trouveras en pièce jointe un fichier calendrier à ajouter à ton agenda.</p>
@endsection
