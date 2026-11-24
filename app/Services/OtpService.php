<?php

namespace App\Services;

use App\Enums\VerificationPurpose;
use App\Models\PhoneVerification;
use App\Models\Setting;
use Illuminate\Support\Facades\Hash;

class OtpService
{
    public function __construct(private readonly SmsSender $sms) {}

    /**
     * Create a fresh OTP for the phone number and deliver it via SMS.
     */
    public function send(string $phone, VerificationPurpose $purpose): string
    {
        PhoneVerification::query()
            ->where('phone', $phone)
            ->where('purpose', $purpose)
            ->whereNull('consumed_at')
            ->update(['consumed_at' => now()]);

        $code = (string) random_int(100000, 999999);
        $ttl = (int) config('bloodbank.otp_ttl_minutes');

        PhoneVerification::query()->create([
            'phone' => $phone,
            'purpose' => $purpose,
            'code_hash' => Hash::make($code),
            'expires_at' => now()->addMinutes($ttl),
            'ip' => request()->ip(),
        ]);

        $this->sms->send($phone, str_replace(
            ['{site}', '{code}', '{minutes}'],
            [(string) Setting::get('site.name', 'One Blood Bank'), $code, (string) $ttl],
            (string) Setting::get('sms.otp_template', '{site}: আপনার যাচাইকরণ কোড {code}। {minutes} মিনিটের মধ্যে ব্যবহার করুন।'),
        ));

        return $code;
    }

    public function verify(string $phone, string $code, VerificationPurpose $purpose): bool
    {
        $record = PhoneVerification::query()
            ->where('phone', $phone)
            ->where('purpose', $purpose)
            ->whereNull('consumed_at')
            ->latest()
            ->first();

        if ($record === null || $record->isExpired() || $record->hasExceededAttempts()) {
            return false;
        }

        if (! $record->matches($code)) {
            $record->increment('attempts');

            return false;
        }

        $record->forceFill(['consumed_at' => now()])->save();

        return true;
    }
}
