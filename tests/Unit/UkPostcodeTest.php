<?php

namespace Tests\Unit;

use App\Rules\UkPostcode;
use PHPUnit\Framework\TestCase;

class UkPostcodeTest extends TestCase
{
    private function validate(string $value): bool
    {
        $passed = true;

        (new UkPostcode)->validate('postcode', $value, function () use (&$passed) {
            $passed = false;
        });

        return $passed;
    }

    public function test_it_accepts_standard_uk_postcodes(): void
    {
        $this->assertTrue($this->validate('SW1A 1AA'));
        $this->assertTrue($this->validate('EC1A 1BB'));
        $this->assertTrue($this->validate('W1A 0AX'));
        $this->assertTrue($this->validate('M1 1AE'));
        $this->assertTrue($this->validate('B1 1BB'));
        $this->assertTrue($this->validate('CR2 6XH'));
        $this->assertTrue($this->validate('DN55 1PT'));
    }

    public function test_it_accepts_postcodes_without_a_space(): void
    {
        $this->assertTrue($this->validate('SW1A1AA'));
        $this->assertTrue($this->validate('M11AE'));
    }

    public function test_it_is_case_insensitive(): void
    {
        $this->assertTrue($this->validate('sw1a 1aa'));
        $this->assertTrue($this->validate('Sw1A 1aA'));
    }

    public function test_it_rejects_invalid_postcodes(): void
    {
        $this->assertFalse($this->validate('12345'));
        $this->assertFalse($this->validate('ABCDE'));
        $this->assertFalse($this->validate('not a postcode'));
        $this->assertFalse($this->validate(''));
        $this->assertFalse($this->validate('SW1A 1CI')); // C and I are disallowed in final two letters
    }

    public function test_it_trims_whitespace_before_validating(): void
    {
        $this->assertTrue($this->validate('  SW1A 1AA  '));
    }
}
