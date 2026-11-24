<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id', 'name', 'email', 'phone', 'amount', 'currency', 'method',
    'transaction_id', 'status', 'message', 'admin_note', 'verified_by', 'verified_at',
])]
class SiteDonation extends Model
{
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'verified_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function scopeVerified(Builder $query): Builder
    {
        return $query->where('status', 'verified');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'verified' => 'যাচাইকৃত',
            'rejected' => 'বাতিল',
            default => 'অপেক্ষমাণ',
        };
    }

    public function statusBadgeClasses(): string
    {
        return match ($this->status) {
            'verified' => 'bg-emerald-50 text-emerald-700',
            'rejected' => 'bg-rose-50 text-rose-700',
            default => 'bg-amber-50 text-amber-700',
        };
    }

    public function methodLabel(): string
    {
        return match ($this->method) {
            'bkash' => 'বিকাশ',
            'nagad' => 'নগদ',
            'rocket' => 'রকেট',
            'bank' => 'ব্যাংক',
            default => 'অন্যান্য',
        };
    }

    public static function methods(): array
    {
        return [
            'bkash' => 'বিকাশ (bKash)',
            'nagad' => 'নগদ (Nagad)',
            'rocket' => 'রকেট (Rocket)',
            'bank' => 'ব্যাংক ট্রান্সফার',
            'other' => 'অন্যান্য',
        ];
    }
}
