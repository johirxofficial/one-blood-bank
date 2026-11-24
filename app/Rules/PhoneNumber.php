<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PhoneNumber implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $phone = static::normalize(is_string($value) ? $value : '');

        if (! preg_match('/^(?:\+?[1-9]|0[1-9])\d{6,14}$/', $phone)) {
            $fail('সঠিক মোবাইল নম্বর দিন — দেশের কোডসহ যেমন +8801712345678 অথবা 01712345678।');
        }
    }

    public static function normalize(?string $value): string
    {
        $value = trim((string) $value);
        $plus = str_starts_with($value, '+');
        $digits = preg_replace('/\D+/', '', $value) ?? '';

        return ($plus ? '+' : '').$digits;
    }
}
