<?php

namespace App\Enums;

enum DonorStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Suspended = 'suspended';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'অপেক্ষমাণ',
            self::Approved => 'ভেরিফাইড',
            self::Rejected => 'বাতিল',
            self::Suspended => 'স্থগিত',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::Pending => 'bg-amber-100 text-amber-800',
            self::Approved => 'bg-emerald-100 text-emerald-800',
            self::Rejected => 'bg-rose-100 text-rose-800',
            self::Suspended => 'bg-slate-200 text-slate-700',
        };
    }
}
