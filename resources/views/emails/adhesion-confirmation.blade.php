@extends('emails.layout')

@section('content')
<p style="font-size: 16px; line-height: 1.6; margin: 0 0 16px;">
    Chère {{ $member->first_name }},
</p>

<p style="font-size: 15px; line-height: 1.6; margin: 0 0 16px;">
    Merci d'avoir confirmé ton inscription !
</p>

<p style="font-size: 15px; line-height: 1.6; margin: 0 0 16px;">
    Tu trouveras en pièce jointe le bulletin de versement pour ta cotisation annuelle.
</p>

<p style="font-size: 15px; line-height: 1.6; margin: 0 0 16px;">
    Ton adhésion devient effective avec la réception du paiement et définitive à la prochaine réunion du comité.
</p>
@endsection
