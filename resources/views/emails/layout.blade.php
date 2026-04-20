<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('association.name') }}</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f5f1e9; font-family: Arial, Helvetica, sans-serif; color: #333333;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: #f5f1e9;">
        <tr>
            <td align="center" style="padding: 30px 0;">
                <table role="presentation" width="600" cellspacing="0" cellpadding="0" border="0" style="max-width: 600px; width: 100%;">
                    {{-- Header --}}
                    <tr>
                        <td align="center" style="background-color: #80081C; padding: 28px 20px; border-radius: 8px 8px 0 0;">
                            <img src="{{ $message->embed(public_path(config('association.logo_path'))) }}" alt="{{ config('association.name') }}" style="max-width: 200px; height: auto;">
                        </td>
                    </tr>
                    {{-- Content --}}
                    <tr>
                        <td style="background-color: #ffffff; padding: 36px 32px; line-height: 1.6; font-size: 15px;">
                            @yield('content')
                        </td>
                    </tr>
                    {{-- Footer --}}
                    <tr>
                        <td align="center" style="background-color: #80081C; padding: 18px 20px; border-radius: 0 0 8px 8px;">
                            <p style="margin: 0; color: #ffffff; font-size: 13px;">
                                {{ config('association.name') }} &mdash; <a href="{{ config('association.website_url') }}" style="color: #ffffff; text-decoration: underline;">{{ preg_replace('#^https?://#', '', config('association.website_url')) }}</a>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
