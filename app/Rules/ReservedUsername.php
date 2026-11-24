<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ReservedUsername implements ValidationRule
{
    /**
     * Usernames that collide with application routes or staff areas.
     *
     * @var array<int, string>
     */
    private const RESERVED = [
        'admin', 'administrator', 'manager', 'staff', 'user', 'users', 'donor', 'donors',
        'api', 'auth', 'login', 'logout', 'register', 'signup', 'signin', 'dashboard',
        'profile', 'settings', 'setting', 'seo', 'ads', 'ad', 'pages', 'page', 'help',
        'support', 'about', 'contact', 'privacy', 'terms', 'card', 'requests', 'request',
        'verify', 'verification', 'password', 'email', 'blood', 'bloodbank', 'thebloodbank',
        'root', 'system', 'official', 'moderator', 'mod', 'null', 'undefined',
    ];

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (in_array(strtolower((string) $value), self::RESERVED, true)) {
            $fail('এই ইউজারনেমটি ব্যবহার করা যাবে না, অন্য নাম বেছে নিন।');
        }
    }
}
