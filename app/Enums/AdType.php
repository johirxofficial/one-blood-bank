<?php

namespace App\Enums;

enum AdType: string
{
    case Banner = 'banner';
    case Popunder = 'popunder';
    case Script = 'script';

    public function label(): string
    {
        return match ($this) {
            self::Banner => 'ব্যানার',
            self::Popunder => 'পপআন্ডার',
            self::Script => 'কাস্টম স্ক্রিপ্ট',
        };
    }
}
