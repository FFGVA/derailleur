/**
 * SGAcademy phone number formatting utility.
 * Depends on: sg-phone-rules.js (must be loaded first).
 *
 * sgPhoneFormat(raw) → { formatted, tel, error }
 *   formatted: display string (e.g. "+41 79 695 97 44")
 *   tel:       tel: URI value (e.g. "+41796959744")
 *   error:     null or error message string
 */
function sgPhoneFormat(raw) {
    if (!raw || raw.trim() === '' || raw.trim() === '—' || raw.trim() === '-') {
        return { formatted: raw || '', tel: '', error: null };
    }

    var input = raw.trim();

    // Validation: only +, digits, and spaces allowed
    if (/[^+0-9\s]/.test(input)) {
        return {
            formatted: input,
            tel: '',
            error: 'Caractères non autorisés (seuls +, chiffres et espaces sont acceptés)'
        };
    }

    // Strip all spaces for processing
    var digits = input.replace(/\s/g, '');

    // Parse leading 00 / 0 / +
    if (digits.indexOf('00') === 0 && digits.charAt(2) !== '0') {
        digits = '+' + digits.substring(2);
    } else if (digits.charAt(0) === '0' && digits.charAt(1) !== '0') {
        digits = '+41' + digits.substring(1);
    }

    // Detect country code and look up rules
    if (digits.charAt(0) === '+') {
        var ccLen = _sgDetectCCLength(digits.substring(1));
        var cc = digits.substring(1, 1 + ccLen);
        var national = digits.substring(1 + ccLen);
        var rule = SG_PHONE_COUNTRIES[cc];

        // Length validation (only for countries that define an expected length)
        if (rule && rule.length !== null && national.length !== rule.length) {
            return {
                formatted: digits,
                tel: '',
                error: 'Numéro ' + rule.label + ' invalide (' + rule.length + ' chiffres attendus après +' + cc + ', ' + national.length + ' trouvés)'
            };
        }
    }

    // Build tel URI (strip everything except + and digits)
    var tel = digits.replace(/[^+0-9]/g, '');

    // Format based on country code
    var formatted = _sgFormatByCountry(digits);

    return { formatted: formatted, tel: tel, error: null };
}

// -- Internal helpers (prefixed _sg to avoid global collisions) ------------

function _sgFormatByCountry(number) {
    if (number.charAt(0) !== '+') {
        return _sgGroupPairs(number);
    }

    var rest = number.substring(1);
    var ccLen = _sgDetectCCLength(rest);
    var cc = rest.substring(0, ccLen);
    var national = rest.substring(ccLen);
    var rule = SG_PHONE_COUNTRIES[cc];

    if (rule) {
        return '+' + cc + ' ' + _sgGroupPattern(national, rule.pattern);
    }

    // Fallback: trailing pairs (odd → leading triple)
    return '+' + cc + ' ' + _sgGroupPairs(national);
}

/**
 * Group digits by an explicit pattern.
 * Extra trailing digits are appended as a final group.
 */
function _sgGroupPattern(digits, groups) {
    var parts = [];
    var pos = 0;
    for (var i = 0; i < groups.length; i++) {
        if (pos >= digits.length) break;
        parts.push(digits.substring(pos, pos + groups[i]));
        pos += groups[i];
    }
    if (pos < digits.length) {
        parts.push(digits.substring(pos));
    }
    return parts.join(' ');
}

/**
 * Group digits in trailing pairs.
 * Even count → nn nn nn nn
 * Odd count  → nnn nn nn nn
 */
function _sgGroupPairs(digits) {
    if (!digits) return '';
    if (digits.length <= 2) return digits;
    var parts = [];
    var start = 0;
    if (digits.length % 2 === 1) {
        parts.push(digits.substring(0, 3));
        start = 3;
    }
    for (var i = start; i < digits.length; i += 2) {
        parts.push(digits.substring(i, i + 2));
    }
    return parts.join(' ');
}

/**
 * Detect ITU country-code length from digits (without the leading +).
 * Uses the tables defined in sg-phone-rules.js.
 */
function _sgDetectCCLength(digits) {
    for (var i = 0; i < SG_PHONE_ITU_ONE_DIGIT.length; i++) {
        if (digits.charAt(0) === SG_PHONE_ITU_ONE_DIGIT[i]) return 1;
    }
    var prefix2 = digits.substring(0, 2);
    for (var i = 0; i < SG_PHONE_ITU_TWO_DIGIT.length; i++) {
        if (SG_PHONE_ITU_TWO_DIGIT[i] === prefix2) return 2;
    }
    return 3;
}
