<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberUtil;

class UkPhoneNumber implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        try {
            $phoneNumberUtil = PhoneNumberUtil::getInstance();
            $phoneNumber = $phoneNumberUtil->parse($value, 'GB');

            if (!$phoneNumberUtil->isValidNumberForRegion($phoneNumber, 'GB')) {
                $fail('The '.$attribute.' must be a valid UK phone number.');
            }
        } catch (NumberParseException $e) {
            $fail('The '.$attribute.' must be a valid UK phone number.');
        }
    }
}
