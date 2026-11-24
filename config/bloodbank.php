<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Donation eligibility
    |--------------------------------------------------------------------------
    |
    | A donor becomes available again after this many months from the last
    | donation date. Bangladesh Blood Transfusion guidelines use 4 months.
    |
    */
    'donation_interval_months' => (int) env('BLOODBANK_DONATION_INTERVAL_MONTHS', 4),

    /*
    |--------------------------------------------------------------------------
    | Phone verification (OTP)
    |--------------------------------------------------------------------------
    */
    'otp_ttl_minutes' => (int) env('BLOODBANK_OTP_TTL_MINUTES', 10),
    'otp_max_attempts' => (int) env('BLOODBANK_OTP_MAX_ATTEMPTS', 5),
    'otp_length' => 6,

    /*
    |--------------------------------------------------------------------------
    | Anti-scraping
    |--------------------------------------------------------------------------
    |
    | Maximum number of donor phone numbers a single account can reveal per
    | day. Keeps bulk scraping and spam callers away from the directory.
    |
    */
    'phone_reveal_daily_limit' => (int) env('BLOODBANK_PHONE_REVEAL_DAILY_LIMIT', 15),

    /*
    |--------------------------------------------------------------------------
    | Donor requirements
    |--------------------------------------------------------------------------
    */
    'minimum_donor_age' => 18,
    'maximum_donor_age' => 65,
    'minimum_donor_weight_kg' => 50,
];
