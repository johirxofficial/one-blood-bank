<?php

namespace App\Enums;

enum BloodGroup: string
{
    case APositive = 'A+';
    case ANegative = 'A-';
    case BPositive = 'B+';
    case BNegative = 'B-';
    case ABPositive = 'AB+';
    case ABNegative = 'AB-';
    case OPositive = 'O+';
    case ONegative = 'O-';

    public function label(): string
    {
        return $this->value;
    }

    public function banglaLabel(): string
    {
        return match ($this) {
            self::APositive => 'এ পজিটিভ',
            self::ANegative => 'এ নেগেটিভ',
            self::BPositive => 'বি পজিটিভ',
            self::BNegative => 'বি নেগেটিভ',
            self::ABPositive => 'এবি পজিটিভ',
            self::ABNegative => 'এবি নেগেটিভ',
            self::OPositive => 'ও পজিটিভ',
            self::ONegative => 'ও নেগেটিভ',
        };
    }

    /**
     * Groups that can receive blood from this donor.
     *
     * @return array<int, self>
     */
    public function canDonateTo(): array
    {
        return match ($this) {
            self::OPositive => [self::OPositive, self::APositive, self::BPositive, self::ABPositive],
            self::ONegative => self::cases(),
            self::APositive => [self::APositive, self::ABPositive],
            self::ANegative => [self::APositive, self::ANegative, self::ABPositive, self::ABNegative],
            self::BPositive => [self::BPositive, self::ABPositive],
            self::BNegative => [self::BPositive, self::BNegative, self::ABPositive, self::ABNegative],
            self::ABPositive => [self::ABPositive],
            self::ABNegative => [self::ABPositive, self::ABNegative],
        };
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        $options = [];

        foreach (self::cases() as $case) {
            $options[$case->value] = $case->value;
        }

        return $options;
    }

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
