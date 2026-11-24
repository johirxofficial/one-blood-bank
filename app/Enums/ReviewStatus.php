<?php

namespace App\Enums;

enum ReviewStatus: string
{
    case Published = 'published';
    case Pending = 'pending';
    case Hidden = 'hidden';

    public function label(): string
    {
        return match ($this) {
            self::Published => 'প্রকাশিত',
            self::Pending => 'অপেক্ষমাণ',
            self::Hidden => 'লুকানো',
        };
    }
}
