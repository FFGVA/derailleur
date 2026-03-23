<?php

return [
    'cotisation_annuelle' => env('FFGVA_COTISATION', 50.00),
    'iban' => env('FFGVA_IBAN', 'CH9580808004931084283'),
    'creditor_name' => 'Fast and Female Geneva',
    'creditor_address' => env('FFGVA_ADDRESS', 'Chemin de Pinchat 42C'),
    'creditor_postal_code' => env('FFGVA_POSTAL_CODE', '1234'),
    'creditor_city' => env('FFGVA_CITY', 'Vessy'),
    'creditor_country' => 'CH',
    'contact_email' => 'fastandfemalegva@etik.com',
    'activation_expiry_hours' => 72,

    'portal_token_expiry_minutes' => 15,
    'portal_session_timeout_minutes' => 300,

    'strava_enabled' => env('FFGVA_STRAVA_ENABLED', false),
];
