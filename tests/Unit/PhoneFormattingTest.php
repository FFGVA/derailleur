<?php

namespace Tests\Unit;

use App\Services\PhoneFormatter;
use PHPUnit\Framework\TestCase;

class PhoneFormattingTest extends TestCase
{
    public function test_swiss_mobile_from_local(): void
    {
        $result = PhoneFormatter::format('0791234567');
        $this->assertEquals('+41 79 123 45 67', $result['formatted']);
        $this->assertEquals('+41791234567', $result['tel']);
        $this->assertNull($result['error']);
    }

    public function test_swiss_mobile_with_international_prefix(): void
    {
        $result = PhoneFormatter::format('+41791234567');
        $this->assertEquals('+41 79 123 45 67', $result['formatted']);
        $this->assertEquals('+41791234567', $result['tel']);
        $this->assertNull($result['error']);
    }

    public function test_swiss_mobile_with_00_prefix(): void
    {
        $result = PhoneFormatter::format('0041791234567');
        $this->assertEquals('+41 79 123 45 67', $result['formatted']);
        $this->assertEquals('+41791234567', $result['tel']);
        $this->assertNull($result['error']);
    }

    public function test_swiss_mobile_with_spaces(): void
    {
        $result = PhoneFormatter::format('079 123 45 67');
        $this->assertEquals('+41 79 123 45 67', $result['formatted']);
        $this->assertNull($result['error']);
    }

    public function test_swiss_landline(): void
    {
        $result = PhoneFormatter::format('0221234567');
        $this->assertEquals('+41 22 123 45 67', $result['formatted']);
        $this->assertNull($result['error']);
    }

    public function test_swiss_wrong_length(): void
    {
        $result = PhoneFormatter::format('+4179123456');
        $this->assertNotNull($result['error']);
        $this->assertStringContainsString('9', $result['error']);
    }

    public function test_french_number(): void
    {
        $result = PhoneFormatter::format('+33612345678');
        $this->assertEquals('+33 612 34 56 78', $result['formatted']);
        $this->assertNull($result['error']);
    }

    public function test_french_wrong_length(): void
    {
        $result = PhoneFormatter::format('+3361234567');
        $this->assertNotNull($result['error']);
    }

    public function test_italian_number(): void
    {
        $result = PhoneFormatter::format('+390234567890');
        $this->assertEquals('+39 023 456 7890', $result['formatted']);
        $this->assertNull($result['error']);
    }

    public function test_german_number(): void
    {
        $result = PhoneFormatter::format('+4915123456789');
        $this->assertEquals('+49 1512 3456789', $result['formatted']);
        $this->assertNull($result['error']);
    }

    public function test_austrian_number(): void
    {
        $result = PhoneFormatter::format('+4366412345678');
        $this->assertEquals('+43 6641 2345678', $result['formatted']);
        $this->assertNull($result['error']);
    }

    public function test_invalid_characters(): void
    {
        $result = PhoneFormatter::format('+41 79-123.45.67');
        $this->assertNotNull($result['error']);
        $this->assertStringContainsString('Caractères', $result['error']);
    }

    public function test_empty_input(): void
    {
        $result = PhoneFormatter::format('');
        $this->assertEquals('', $result['formatted']);
        $this->assertNull($result['error']);
    }

    public function test_null_input(): void
    {
        $result = PhoneFormatter::format(null);
        $this->assertEquals('', $result['formatted']);
        $this->assertNull($result['error']);
    }

    public function test_unknown_country_fallback_pairs(): void
    {
        // +351 Portugal (3-digit code, no specific rule)
        $result = PhoneFormatter::format('+351912345678');
        $this->assertStringStartsWith('+351 ', $result['formatted']);
        $this->assertNull($result['error']);
    }
}
