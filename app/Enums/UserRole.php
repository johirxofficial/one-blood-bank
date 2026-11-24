<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Manager = 'manager';
    case Staff = 'staff';
    case User = 'user';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'অ্যাডমিন',
            self::Manager => 'ম্যানেজার',
            self::Staff => 'স্টাফ',
            self::User => 'ইউজার',
        };
    }

    public function isStaff(): bool
    {
        return $this !== self::User;
    }

    public function canManageUsers(): bool
    {
        return in_array($this, [self::Admin, self::Manager], true);
    }

    public function canManageSettings(): bool
    {
        return $this === self::Admin;
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        $options = [];

        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }

        return $options;
    }
}
