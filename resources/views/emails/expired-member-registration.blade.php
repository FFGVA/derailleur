@extends('emails.layout')

@section('content')
    <p style="margin-bottom: 16px;"><strong>Attention :</strong> une membre dont l'adhésion est expirée s'est inscrite à un événement.</p>

    <table role="presentation" cellspacing="0" cellpadding="0" border="0" style="margin-bottom: 16px; font-size: 14px;">
        <tr>
            <td style="padding: 4px 16px 4px 0; color: #666;">Membre</td>
            <td style="padding: 4px 0; font-weight: 500;">{{ $member->first_name }} {{ $member->last_name }}@if($member->member_number) (n° {{ $member->member_number }})@endif</td>
        </tr>
        <tr>
            <td style="padding: 4px 16px 4px 0; color: #666;">E-mail</td>
            <td style="padding: 4px 0;">{{ $member->email }}</td>
        </tr>
        <tr>
            <td style="padding: 4px 16px 4px 0; color: #666;">Fin d'adhésion</td>
            <td style="padding: 4px 0; font-weight: 500; color: #991b1b;">{{ $member->membership_end->format('d.m.Y') }}</td>
        </tr>
        <tr>
            <td style="padding: 4px 16px 4px 0; color: #666;">Événement</td>
            <td style="padding: 4px 0; font-weight: 500;">{{ $event->title }} — {{ $event->starts_at->format('d.m.Y H:i') }}</td>
        </tr>
    </table>
@endsection
