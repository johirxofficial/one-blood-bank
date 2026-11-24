<?php

namespace App\Models;

use App\Enums\VerificationPurpose;
use Database\Factories\PhoneVerificationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

#[Fillable(['phone', 'purpose', 'code_hash', 'attempts', 'expires_at', 'consumed_at', 'ip'])]
#[Hidden(['code_hash'])]
class PhoneVerification extends Model
{
    /** @use HasFactory<PhoneVerificationFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'purpose' => VerificationPurpose::class,
            'expires_at' => 'datetime',
            'consumed_at' => 'datetime',
            'attempts' => 'integer',
        ];
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function isConsumed(): bool
    {
        return $this->consumed_at !== null;
    }

    public function hasExceededAttempts(): bool
    {
        return $this->attempts >= (int) config('bloodbank.otp_max_attempts');
    }

    public function matches(string $code): bool
    {
        return Hash::check($code, $this->code_hash);
    }
}
