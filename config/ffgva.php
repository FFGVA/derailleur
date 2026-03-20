<?php

return [
    'cotisation_annuelle' => env('FFGVA_COTISATION', 50.00),
    'iban' => env('FFGVA_IBAN', 'CH5906824650091667849'),
    'creditor_name' => 'Fast and Female Geneva',
    'creditor_address' => env('FFGVA_ADDRESS', 'Champ Baron 14a'),
    'creditor_postal_code' => env('FFGVA_POSTAL_CODE', '1209'),
    'creditor_city' => env('FFGVA_CITY', 'Genève'),
    'creditor_country' => 'CH',
    'activation_expiry_hours' => 72,
];
