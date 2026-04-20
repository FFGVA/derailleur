@extends('emails.layout')

@section('content')
    <p style="margin-bottom: 16px;">La membre <strong>{{ $member->first_name }} {{ $member->last_name }}</strong>@if($member->member_number) (n° {{ $member->member_number }})@endif demande les modifications suivantes :</p>

    <table role="presentation" cellspacing="0" cellpadding="0" border="0" style="width: 100%; margin-bottom: 16px; font-size: 14px;">
        <tr style="background-color: {{ config('association.colors.background') }};">
            <td style="padding: 6px 12px; font-weight: 600; width: 40%;">Champ</td>
            <td style="padding: 6px 12px; font-weight: 600;">Actuel</td>
            <td style="padding: 6px 12px; font-weight: 600;">Demandé</td>
        </tr>
        @php
            $fields = [
                'first_name' => ['label' => 'Prénom', 'current' => $member->first_name],
                'last_name' => ['label' => 'Nom', 'current' => $member->last_name],
                'email' => ['label' => 'E-mail', 'current' => $member->email],
                'address' => ['label' => 'Adresse', 'current' => $member->address ?? ''],
                'postal_code' => ['label' => 'NPA', 'current' => $member->postal_code ?? ''],
                'city' => ['label' => 'Ville', 'current' => $member->city ?? ''],
            ];
        @endphp
        @foreach($fields as $key => $field)
            @if(isset($changes[$key]) && $changes[$key] !== $field['current'])
                <tr>
                    <td style="padding: 6px 12px; color: #666;">{{ $field['label'] }}</td>
                    <td style="padding: 6px 12px;">{{ $field['current'] }}</td>
                    <td style="padding: 6px 12px; font-weight: 500; color: {{ config('association.colors.primary') }};">{{ $changes[$key] }}</td>
                </tr>
            @endif
        @endforeach
    </table>

    @if(!empty($changes['phones']))
        <p style="margin-bottom: 8px; font-weight: 600;">Téléphones demandés :</p>
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" style="width: 100%; margin-bottom: 16px; font-size: 14px;">
            <tr style="background-color: {{ config('association.colors.background') }};">
                <td style="padding: 6px 12px; font-weight: 600;">Libellé</td>
                <td style="padding: 6px 12px; font-weight: 600;">Numéro</td>
                <td style="padding: 6px 12px; font-weight: 600;">WhatsApp</td>
            </tr>
            @foreach($changes['phones'] as $phone)
                <tr>
                    <td style="padding: 6px 12px;">{{ $phone['label'] ?? '—' }}</td>
                    <td style="padding: 6px 12px; font-weight: 500;">{{ $phone['number'] }}</td>
                    <td style="padding: 6px 12px;">{{ isset($phone['whatsapp']) ? 'Oui' : 'Non' }}</td>
                </tr>
            @endforeach
        </table>

        @if($member->phones->isNotEmpty())
            <p style="margin-bottom: 8px; font-size: 13px; color: #666;">Téléphones actuels :</p>
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" style="width: 100%; margin-bottom: 16px; font-size: 13px; color: #666;">
                @foreach($member->phones as $phone)
                    <tr>
                        <td style="padding: 4px 12px;">{{ $phone->label ?? '—' }}</td>
                        <td style="padding: 4px 12px;">{{ $phone->phone_number }}</td>
                        <td style="padding: 4px 12px;">{{ $phone->is_whatsapp ? 'WA' : '' }}</td>
                    </tr>
                @endforeach
            </table>
        @endif
    @endif

    <p style="font-size: 13px; color: #666;">Répondre à cet e-mail pour contacter la membre directement.</p>
@endsection
