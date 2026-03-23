@extends('emails.layout')

@section('content')
<p style="font-size: 16px; line-height: 1.6; margin: 0 0 16px;">
    Chère {{ $member->first_name }},
</p>

<p style="font-size: 15px; line-height: 1.6; margin: 0 0 16px;">
    Merci d'avoir confirmé ton inscription !
</p>

<p style="font-size: 15px; line-height: 1.6; margin: 0 0 16px;">
    Tu recevras ta facture de cotisation dans un email séparé.
</p>

<p style="font-size: 15px; line-height: 1.6; margin: 0 0 16px;">
    Ton adhésion devient effective avec la réception du paiement et définitive à la prochaine réunion du comité.
</p>

<p style="font-size: 13px; line-height: 1.6; margin: 24px 0 0; padding: 16px; background-color: #f8f8f8; border-radius: 4px; color: #666666;">
    En confirmant ton inscription, tu consens au stockage et à l'utilisation de l'ensemble des informations transmises, dans la mesure nécessaire à la poursuite des objectifs de l'association, conformément à la Loi fédérale sur la protection des données (LPD). Tu peux consulter notre déclaration complète de protection des données sur ton espace membre.
</p>
@endsection
