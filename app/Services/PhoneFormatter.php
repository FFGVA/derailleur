<?php

namespace App\Services;

/**
 * Phone number formatting utility.
 * Port of sg-phone.js / sg-phone-rules.js from the SG Academy project.
 *
 * PhoneFormatter::format($raw) → ['formatted' => ..., 'tel' => ..., 'error' => ...]
 */
class PhoneFormatter
{
    private const COUNTRIES = [
        '41' => ['pattern' => [2, 3, 2, 2], 'length' => 9, 'label' => 'suisse'],
        '33' => ['pattern' => [3, 2, 2, 2], 'length' => 9, 'label' => 'français'],
        '39' => ['pattern' => [3, 3, 4], 'length' => null, 'label' => 'italien'],
        '49' => ['pattern' => [4, 7], 'length' => null, 'label' => 'allemand'],
        '43' => ['pattern' => [4, 7], 'length' => null, 'label' => 'autrichien'],
    ];

    private const ITU_ONE_DIGIT = ['1', '7'];

    private const ITU_TWO_DIGIT = [
        '20', '27',
        '30', '31', '32', '33', '34', '36', '39',
        '40', '41', '43', '44', '45', '46', '47', '48', '49',
        '51', '52', '53', '54', '55', '56', '57', '58',
        '60', '61', '62', '63', '64', '65', '66',
        '81', '82', '84', '86',
        '90', '91', '92', '93', '94', '95', '98',
    ];

    public static function format(?string $raw): array
    {
        if ($raw === null || trim($raw) === '' || trim($raw) === '—' || trim($raw) === '-') {
            return ['formatted' => $raw ?? '', 'tel' => '', 'error' => null];
        }

        $input = trim($raw);

        // Only +, digits and spaces allowed
        if (preg_match('/[^+0-9\s]/', $input)) {
            return [
                'formatted' => $input,
                'tel' => '',
                'error' => 'Caractères non autorisés (seuls +, chiffres et espaces sont acceptés)',
            ];
        }

        // Strip spaces for processing
        $digits = str_replace(' ', '', $input);

        // Normalize prefixes
        if (str_starts_with($digits, '00') && ($digits[2] ?? '0') !== '0') {
            $digits = '+' . substr($digits, 2);
        } elseif (str_starts_with($digits, '0') && ($digits[1] ?? '0') !== '0') {
            $digits = '+41' . substr($digits, 1);
        }

        // Detect country code and validate length
        if (str_starts_with($digits, '+')) {
            $rest = substr($digits, 1);
            $ccLen = self::detectCCLength($rest);
            $cc = substr($rest, 0, $ccLen);
            $national = substr($rest, $ccLen);
            $rule = self::COUNTRIES[$cc] ?? null;

            if ($rule && $rule['length'] !== null && strlen($national) !== $rule['length']) {
                return [
                    'formatted' => $digits,
                    'tel' => '',
                    'error' => "Numéro {$rule['label']} invalide ({$rule['length']} chiffres attendus après +{$cc}, " . strlen($national) . ' trouvés)',
                ];
            }
        }

        $tel = preg_replace('/[^+0-9]/', '', $digits);
        $formatted = self::formatByCountry($digits);

        return ['formatted' => $formatted, 'tel' => $tel, 'error' => null];
    }

    private static function formatByCountry(string $number): string
    {
        if (!str_starts_with($number, '+')) {
            return self::groupPairs($number);
        }

        $rest = substr($number, 1);
        $ccLen = self::detectCCLength($rest);
        $cc = substr($rest, 0, $ccLen);
        $national = substr($rest, $ccLen);
        $rule = self::COUNTRIES[$cc] ?? null;

        if ($rule) {
            return '+' . $cc . ' ' . self::groupPattern($national, $rule['pattern']);
        }

        return '+' . $cc . ' ' . self::groupPairs($national);
    }

    private static function groupPattern(string $digits, array $groups): string
    {
        $parts = [];
        $pos = 0;
        foreach ($groups as $size) {
            if ($pos >= strlen($digits)) {
                break;
            }
            $parts[] = substr($digits, $pos, $size);
            $pos += $size;
        }
        if ($pos < strlen($digits)) {
            $parts[] = substr($digits, $pos);
        }
        return implode(' ', $parts);
    }

    private static function groupPairs(string $digits): string
    {
        if (!$digits || strlen($digits) <= 2) {
            return $digits ?: '';
        }

        $parts = [];
        $start = 0;
        if (strlen($digits) % 2 === 1) {
            $parts[] = substr($digits, 0, 3);
            $start = 3;
        }
        for ($i = $start; $i < strlen($digits); $i += 2) {
            $parts[] = substr($digits, $i, 2);
        }
        return implode(' ', $parts);
    }

    private static function detectCCLength(string $digits): int
    {
        if (in_array($digits[0], self::ITU_ONE_DIGIT, true)) {
            return 1;
        }
        $prefix2 = substr($digits, 0, 2);
        if (in_array($prefix2, self::ITU_TWO_DIGIT, true)) {
            return 2;
        }
        return 3;
    }
}
