@extends('emails.layout')

@section('content')
<p style="font-size: 16px; line-height: 1.6; margin: 0 0 16px;">
    Chère {{ $member->first_name }},
</p>

<p style="font-size: 15px; line-height: 1.6; margin: 0 0 16px;">
    Nous sommes très heureuses de ton inscription !
</p>

<p style="font-size: 15px; line-height: 1.6; margin: 0 0 16px;">
    Pour finaliser ton adhésion, confirme ton adresse email en cliquant sur le bouton ci-dessous :
</p>

<p style="text-align: center; margin: 28px 0;">
    <a href="{{ $activationUrl }}" style="display: inline-block; background-color: #80081C; color: #ffffff; text-decoration: none; padding: 14px 32px; border-radius: 6px; font-size: 16px; font-weight: bold;">
        Confirmer mon adresse email
    </a>
</p>

<p style="font-size: 13px; line-height: 1.6; margin: 24px 0 0; padding: 16px; background-color: #f8f8f8; border-radius: 4px; color: #666666;">
    Tes données personnelles sont traitées conformément à la Loi fédérale sur la protection des données (LPD). Elles sont utilisées exclusivement dans le cadre de la gestion de l'association.
</p>
@endsection
