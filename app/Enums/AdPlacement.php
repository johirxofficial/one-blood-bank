<?php

namespace App\Enums;

enum AdPlacement: string
{
    case Header = 'header';
    case Sidebar = 'sidebar';
    case DonorList = 'donor_list';
    case Footer = 'footer';

    public function label(): string
    {
        return match ($this) {
            self::Header => 'হেডার',
            self::Sidebar => 'সাইডবার',
            self::DonorList => 'ডোনার লিস্টের মাঝে',
            self::Footer => 'ফুটার',
        };
    }
}
