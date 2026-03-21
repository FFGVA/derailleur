@extends('emails.layout')

@section('content')
    <p style="margin-bottom: 16px;">Bonjour {{ $member->first_name }},</p>

    <p style="margin-bottom: 20px;">Tu as demandé un lien de connexion à ton espace membre Fast and Female Geneva.</p>

    <p style="text-align: center; margin-bottom: 20px;">
        <a href="{{ $magicLinkUrl }}" style="display: inline-block; background-color: #80081C; color: #ffffff; font-weight: 600; font-size: 15px; padding: 12px 32px; border-radius: 6px; text-decoration: none;">
            Me connecter
        </a>
    </p>

    <p style="margin-bottom: 16px; font-size: 13px; color: #666;">
        Ce lien expire le {{ $expiresAt }}. Si tu n'as pas fait cette demande, ignore cet e-mail.
    </p>
@endsection
