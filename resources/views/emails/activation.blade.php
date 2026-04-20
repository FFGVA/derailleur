@extends('emails.layout')

@section('content')
<p style="font-size: 16px; line-height: 1.6; margin: 0 0 16px;">
    Chère {{ $member->first_name }},
</p>

<p style="font-size: 15px; line-height: 1.6; margin: 0 0 16px;">
    Un immense merci pour ton adhésion, nous avons bien reçu ton paiement ! Nous sommes trop contentes de t'avoir parmi nous pour cette saison.
</p>

<p style="font-size: 15px; line-height: 1.6; margin: 0 0 16px;">
    Tu peux dès aujourd'hui télécharger ta carte de membre dans l'application : <a href="https://derailleur.ffgva.ch/login" style="color: {{ config('association.colors.primary') }}; text-decoration: underline;">derailleur.ffgva.ch/login</a> — puis tu recevras un email avec un lien pour te connecter. Ensuite, sur la page d'accueil, tu as en haut à droite un petit logo symbolisant un QR-code qui t'affiche ta preuve d'adhésion. Sur cette page, tu peux télécharger ta carte en format PDF.
</p>

<p style="font-size: 15px; line-height: 1.6; margin: 0 0 16px;">
    Avec cette carte, tu as accès à de nombreuses réductions chez nos partenaires et des tarifs préférentiels sur courses et événements. De plus tu as un accès prioritaire à tous nos ateliers.
</p>

<p style="font-size: 15px; line-height: 1.6; margin: 0 0 16px;">
    On a hâte de te retrouver sur la route !
</p>

<p style="font-size: 15px; line-height: 1.6; margin: 0 0 0;">
    Sportivement,<br>
    L'équipe FFGVA
</p>
@endsection
