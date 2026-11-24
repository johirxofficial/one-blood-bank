<?php

namespace App\Support;

use App\Models\Setting;

class LocationCountry
{
    /**
     * ISO-3166 alpha-2 codes for the country list shown by the installer.
     *
     * @var array<string, string>
     */
    private const NAMES = [
        'Bangladesh' => 'BD',
        'India' => 'IN',
        'Pakistan' => 'PK',
        'Nepal' => 'NP',
        'Bhutan' => 'BT',
        'Sri Lanka' => 'LK',
        'Maldives' => 'MV',
        'Afghanistan' => 'AF',
        'Myanmar' => 'MM',
        'Malaysia' => 'MY',
        'Singapore' => 'SG',
        'Indonesia' => 'ID',
        'Thailand' => 'TH',
        'Vietnam' => 'VN',
        'Philippines' => 'PH',
        'China' => 'CN',
        'Japan' => 'JP',
        'South Korea' => 'KR',
        'Saudi Arabia' => 'SA',
        'United Arab Emirates' => 'AE',
        'Qatar' => 'QA',
        'Kuwait' => 'KW',
        'Bahrain' => 'BH',
        'Oman' => 'OM',
        'Jordan' => 'JO',
        'Lebanon' => 'LB',
        'Iraq' => 'IQ',
        'Iran' => 'IR',
        'Turkey' => 'TR',
        'Egypt' => 'EG',
        'Libya' => 'LY',
        'Sudan' => 'SD',
        'South Africa' => 'ZA',
        'Nigeria' => 'NG',
        'Kenya' => 'KE',
        'Ghana' => 'GH',
        'Ethiopia' => 'ET',
        'United Kingdom' => 'GB',
        'Ireland' => 'IE',
        'France' => 'FR',
        'Germany' => 'DE',
        'Italy' => 'IT',
        'Spain' => 'ES',
        'Portugal' => 'PT',
        'Netherlands' => 'NL',
        'Belgium' => 'BE',
        'Sweden' => 'SE',
        'Norway' => 'NO',
        'Denmark' => 'DK',
        'Finland' => 'FI',
        'Switzerland' => 'CH',
        'Austria' => 'AT',
        'Poland' => 'PL',
        'Greece' => 'GR',
        'Russia' => 'RU',
        'Ukraine' => 'UA',
        'United States' => 'US',
        'Canada' => 'CA',
        'Mexico' => 'MX',
        'Brazil' => 'BR',
        'Argentina' => 'AR',
        'Chile' => 'CL',
        'Australia' => 'AU',
        'New Zealand' => 'NZ',
    ];

    /**
     * The ISO-3166 alpha-2 country code whose location hierarchy is shown.
     * Administrators can switch this from Settings → Others.
     */
    public static function code(): string
    {
        try {
            $code = strtoupper((string) Setting::get('others.location_country_code', 'BD'));
        } catch (\Throwable) {
            return 'BD';
        }

        return preg_match('/^[A-Z]{2}$/', $code) === 1 ? $code : 'BD';
    }

    public static function codeForName(string $country): string
    {
        return self::NAMES[$country] ?? 'BD';
    }
}
