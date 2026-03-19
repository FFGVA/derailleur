/**
 * SGAcademy — Phone number formatting rules.
 *
 * This file defines all country-specific knowledge used by sg-phone.js:
 *   - SG_PHONE_COUNTRIES: per-country formatting and validation
 *   - SG_PHONE_ITU:       ITU country-code length detection
 *
 * To add or modify a country, edit the relevant entry below.
 * sg-phone.js consumes these tables and never hard-codes country logic.
 */

// ---------------------------------------------------------------------------
// Country-specific formatting rules
// ---------------------------------------------------------------------------
//
// Each entry is keyed by the dial code (digits only, no "+").
//
//   pattern  – array of group sizes applied to the NATIONAL part
//              (digits after the country code).
//              Example: [2, 3, 2, 2] means "XX XXX XX XX".
//
//   length   – expected number of national digits, or null if variable.
//              When set, the formatter rejects numbers that don't match.
//
//   label    – country name shown in validation error messages (French).
//
//   note     – free-text comment for maintainers (not used at runtime).
//
var SG_PHONE_COUNTRIES = {

    // Switzerland  +41 XX XXX XX XX
    // Always 9 national digits.  Leading 0 is stripped (standard GSM).
    '41': {
        pattern: [2, 3, 2, 2],
        length: 9,
        label: 'suisse',
        note: 'Mobile 07x, landline 02x/03x/04x/05x/06x'
    },

    // France  +33 XXX XX XX XX
    // Always 9 national digits.  Leading 0 is stripped.
    '33': {
        pattern: [3, 2, 2, 2],
        length: 9,
        label: 'français',
        note: 'Mobile 06/07, landline 01-05/08/09'
    },

    // Italy  +39 XXX XXX XXXX
    // The leading 0 is part of the national number and must be kept.
    // Length varies (landline 6-11, mobile 10 digits) — no strict check.
    '39': {
        pattern: [3, 3, 4],
        length: null,
        label: 'italien',
        note: 'Leading 0 is significant; mobile starts with 3'
    },

    // Germany  +49 XXXX XXXXXXX
    // Area-code length varies (2-5 digits) — generic two-group split.
    '49': {
        pattern: [4, 7],
        length: null,
        label: 'allemand',
        note: 'Variable-length area codes; no strict digit count'
    },

    // Austria  +43 XXXX XXXXXXX
    // Same variability as Germany.
    '43': {
        pattern: [4, 7],
        length: null,
        label: 'autrichien',
        note: 'Variable-length area codes; no strict digit count'
    }
};

// ---------------------------------------------------------------------------
// ITU country-code length detection
// ---------------------------------------------------------------------------
//
// ITU-T E.164 assigns country codes of 1, 2 or 3 digits:
//
//   1 digit  — +1 (North America / NANP), +7 (Russia, Kazakhstan)
//
//   2 digits — Most of Europe, large Asian/American countries.
//              Listed exhaustively in ITU_TWO_DIGIT_CODES below.
//
//   3 digits — Everything else (small European states like +351 Portugal,
//              +352 Luxembourg, +353 Ireland, +370 Lithuania, +420 Czechia,
//              +421 Slovakia, +423 Liechtenstein; Caribbean +1-xxx handled
//              via NANP under +1; African +2xx; Pacific +6xx; etc.)
//
// Detection logic:
//   1. Starts with 1 or 7  →  1-digit code
//   2. First two digits in ITU_TWO_DIGIT_CODES  →  2-digit code
//   3. Otherwise  →  3-digit code
//
var SG_PHONE_ITU_ONE_DIGIT = ['1', '7'];

var SG_PHONE_ITU_TWO_DIGIT = [
    // Zone 2 — Africa
    '20',   // Egypt
    '27',   // South Africa

    // Zone 3 — Europe
    '30',   // Greece
    '31',   // Netherlands
    '32',   // Belgium
    '33',   // France
    '34',   // Spain
    '36',   // Hungary
    '39',   // Italy

    // Zone 4 — Europe
    '40',   // Romania
    '41',   // Switzerland
    '43',   // Austria
    '44',   // United Kingdom
    '45',   // Denmark
    '46',   // Sweden
    '47',   // Norway
    '48',   // Poland
    '49',   // Germany

    // Zone 5 — Americas
    '51',   // Peru
    '52',   // Mexico
    '53',   // Cuba
    '54',   // Argentina
    '55',   // Brazil
    '56',   // Chile
    '57',   // Colombia
    '58',   // Venezuela

    // Zone 6 — Southeast Asia & Oceania
    '60',   // Malaysia
    '61',   // Australia
    '62',   // Indonesia
    '63',   // Philippines
    '64',   // New Zealand
    '65',   // Singapore
    '66',   // Thailand

    // Zone 8 — East Asia
    '81',   // Japan
    '82',   // South Korea
    '84',   // Vietnam
    '86',   // China

    // Zone 9 — South/West/Central Asia
    '90',   // Turkey
    '91',   // India
    '92',   // Pakistan
    '93',   // Afghanistan
    '94',   // Sri Lanka
    '95',   // Myanmar
    '98'    // Iran
];
