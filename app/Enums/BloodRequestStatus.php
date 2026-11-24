<?php

namespace App\Enums;

enum BloodRequestStatus: string
{
    case Open = 'open';
    case Fulfilled = 'fulfilled';
    case Expired = 'expired';

    public function label(): string
    {
        return match ($this) {
            self::Open => 'চলমান',
            self::Fulfilled => 'পূরণ হয়েছে',
            self::Expired => 'মেয়াদ শেষ',
        };
    }
}
