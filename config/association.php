<?php

/*
|--------------------------------------------------------------------------
| Association Identity & Branding
|--------------------------------------------------------------------------
|
| Single source of truth for everything that makes this application
| belong to a specific association. To rebrand for another club,
| edit this file and replace the logo asset.
|
| .env is reserved for secrets (DB password, SMTP) and per-environment
| values (APP_URL, APP_DEBUG). The brand never goes in .env.
|
*/

return [

    // ── Organisation ──
    'name' => 'Fast and Female Geneva',
    'short_name' => 'FFGVA',
    'website_url' => 'https://www.ffgva.ch',
    'logo_path' => 'images/logo-ffgva.png', // relative to public/

    // ── Contact ──
    'contact_email' => 'fastandfemalegva@etik.com',

    // ── Email sending ──
    'mail_from_address' => 'noreply@ffgva.ch',
    'mail_from_name' => 'Fast and Female Geneva - Ne pas répondre',
    'mail_reply_to_address' => 'fastandfemalegva@etik.com',
    'mail_reply_to_name' => 'Fast and Female Geneva',

    // ── Creditor / invoicing ──
    'iban' => 'CH9580808004931084283',
    'creditor_name' => 'Fast and Female Geneva',
    'creditor_address' => 'Chemin de Pinchat 42C',
    'creditor_postal_code' => '1234',
    'creditor_city' => 'Vessy',
    'creditor_country' => 'CH',
    'cotisation_annuelle' => 50.00,
    'currency' => 'CHF',

    // ── Colors ──
    'colors' => [
        'primary' => '#80081C',
        'primary_hover' => '#660616',
        'background' => '#f5f1e9',
        // RGB arrays for FPDF (InvoiceService PDF generation)
        'pdf_brand_rgb' => [128, 8, 28],
        'pdf_text_dark_rgb' => [51, 51, 51],
        'pdf_text_light_rgb' => [102, 102, 102],
        'pdf_separator_rgb' => [200, 200, 200],
    ],

    // ── Portal & security ──
    'activation_expiry_hours' => 72,
    'portal_token_expiry_minutes' => 15,
    'portal_session_timeout_minutes' => 300,

    // ── Strava integration ──
    'strava_enabled' => env('STRAVA_ENABLED', false),
    'strava_client_id' => env('STRAVA_CLIENT_ID'),
    'strava_client_secret' => env('STRAVA_CLIENT_SECRET'),
    'strava_club_id' => env('STRAVA_CLUB_ID'),

];
