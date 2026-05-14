<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class UkPostcode implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! preg_match('/^[A-Z]{1,2}[0-9][0-9A-Z]?\s?[0-9][ABD-HJLNP-UW-Z]{2}$/i', trim((string) $value))) {
            $fail('The :attribute must be a valid UK postcode.');
        }
    }
}
