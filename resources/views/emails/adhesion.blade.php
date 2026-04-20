@extends('emails.layout')

@section('content')
    <h2 style="color: {{ config('association.colors.primary') }}; margin: 0 0 20px; font-size: 20px;">Nouvelle demande d'adhésion</h2>

    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
        <tr>
            <td style="padding: 8px 0; font-weight: bold; width: 160px; color: #555; vertical-align: top;">Nom</td>
            <td style="padding: 8px 0;">{{ $nom }}</td>
        </tr>
        <tr>
            <td style="padding: 8px 0; font-weight: bold; color: #555; vertical-align: top;">Prénom</td>
            <td style="padding: 8px 0;">{{ $prenom }}</td>
        </tr>
        <tr>
            <td style="padding: 8px 0; font-weight: bold; color: #555; vertical-align: top;">Courriel</td>
            <td style="padding: 8px 0;"><a href="mailto:{{ $email }}" style="color: {{ config('association.colors.primary') }};">{{ $email }}</a></td>
        </tr>
        <tr>
            <td style="padding: 8px 0; font-weight: bold; color: #555; vertical-align: top;">Téléphone</td>
            <td style="padding: 8px 0;"><a href="tel:{{ $telephone }}" style="color: {{ config('association.colors.primary') }};">{{ $telephone }}</a></td>
        </tr>
        <tr>
            <td style="padding: 8px 0; font-weight: bold; color: #555; vertical-align: top;">Photos/vidéos</td>
            <td style="padding: 8px 0;">{{ $photo_ok }}</td>
        </tr>
    </table>

    @if($type_velo || $sorties || $atelier || $instagram || $strava || $statuts_ok || $cotisation_ok)
        <hr style="border: none; border-top: 1px solid #d9bbae; margin: 20px 0;">
        <h3 style="color: {{ config('association.colors.primary') }}; margin: 0 0 12px; font-size: 15px;">Informations complémentaires</h3>

        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
            @if($type_velo)
            <tr>
                <td style="padding: 5px 0; font-weight: bold; width: 160px; color: #555;">Type de vélo</td>
                <td style="padding: 5px 0;">{{ $type_velo }}</td>
            </tr>
            @endif
            @if($sorties)
            <tr>
                <td style="padding: 5px 0; font-weight: bold; color: #555;">Sorties souhaitées</td>
                <td style="padding: 5px 0;">{{ $sorties }}</td>
            </tr>
            @endif
            @if($atelier)
            <tr>
                <td style="padding: 5px 0; font-weight: bold; color: #555;">Atelier souhaité</td>
                <td style="padding: 5px 0;">{{ $atelier }}</td>
            </tr>
            @endif
            @if($instagram)
            <tr>
                <td style="padding: 5px 0; font-weight: bold; color: #555;">Instagram</td>
                <td style="padding: 5px 0;">{{ $instagram }}</td>
            </tr>
            @endif
            @if($strava)
            <tr>
                <td style="padding: 5px 0; font-weight: bold; color: #555;">Strava</td>
                <td style="padding: 5px 0;">{{ $strava }}</td>
            </tr>
            @endif
            @if($statuts_ok)
            <tr>
                <td style="padding: 5px 0; font-weight: bold; color: #555;">Statuts acceptés</td>
                <td style="padding: 5px 0;">{{ $statuts_ok }}</td>
            </tr>
            @endif
            @if($cotisation_ok)
            <tr>
                <td style="padding: 5px 0; font-weight: bold; color: #555;">Cotisation acceptée</td>
                <td style="padding: 5px 0;">{{ $cotisation_ok }}</td>
            </tr>
            @endif
        </table>
    @endif
@endsection
