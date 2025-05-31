<?php

declare(strict_types=1);

namespace Atendwa\Support\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class JsonObjectOrArray implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (is_array($value)) {
            return;
        }

        if (! is_string($value)) {
            $fail('The :attribute must be a valid array.');

            return;
        }

        $decoded = json_decode($value, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $fail('The :attribute must be a valid array.');
        }

        if (! is_array($decoded)) {
            $fail('The :attribute must be a valid array.');
        }
    }
}
