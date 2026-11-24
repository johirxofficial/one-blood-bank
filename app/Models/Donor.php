<?php

namespace App\Models;

use App\Enums\DonorStatus;
use App\Enums\ReviewStatus;
use Database\Factories\DonorFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

#[Fillable([
    'user_id', 'blood_group', 'country', 'state', 'city', 'division_id', 'district_id', 'upazila_id', 'union_id',
    'village', 'address_details', 'date_of_birth', 'gender', 'weight_kg',
    'last_donation_date', 'available_from', 'is_available', 'status',
    'verified_at', 'verified_by', 'rejection_reason', 'total_donations', 'is_public',
])]
class Donor extends Model
{
    /** @use HasFactory<DonorFactory> */
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'status' => DonorStatus::class,
            'date_of_birth' => 'date',
            'last_donation_date' => 'date',
            'available_from' => 'date',
            'verified_at' => 'datetime',
            'is_available' => 'boolean',
            'is_public' => 'boolean',
            'weight_kg' => 'integer',
            'total_donations' => 'integer',
            'rating_avg' => 'decimal:2',
            'rating_count' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Donor $donor): void {
            $donor->available_from = $donor->last_donation_date
                ?->copy()
                ->addMonths((int) config('bloodbank.donation_interval_months'));

            $donor->is_available = $donor->available_from === null || $donor->available_from->isPast();
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function upazila(): BelongsTo
    {
        return $this->belongsTo(Upazila::class);
    }

    public function union(): BelongsTo
    {
        return $this->belongsTo(Union::class);
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(DonorContact::class)->orderBy('id');
    }

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }

    public function phoneReveals(): HasMany
    {
        return $this->hasMany(PhoneReveal::class);
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', DonorStatus::Approved)
            ->where('is_public', true)
            ->whereHas('user');
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('is_available', true);
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        return $query->where(function (Builder $query) use ($term): void {
            $query->where('village', 'like', "%{$term}%")
                ->orWhere('city', 'like', "%{$term}%")
                ->orWhere('state', 'like', "%{$term}%")
                ->orWhere('country', 'like', "%{$term}%")
                ->orWhere('address_details', 'like', "%{$term}%")
                ->orWhereHas('user', function (Builder $query) use ($term): void {
                    $query->where('name', 'like', "%{$term}%")
                        ->orWhere('username', 'like', "%{$term}%")
                        ->orWhere('phone', 'like', "%{$term}%");
                })
                ->orWhereHas('upazila', fn (Builder $query) => $query->where('name', 'like', "%{$term}%")
                    ->orWhere('name_bn', 'like', "%{$term}%"))
                ->orWhereHas('district', fn (Builder $query) => $query->where('name', 'like', "%{$term}%")
                    ->orWhere('name_bn', 'like', "%{$term}%"));
        });
    }

    /**
     * Apply the public directory filters.
     *
     * @param  array<string, mixed>  $filters
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when(filled($filters['q'] ?? null), fn (Builder $query) => $query->search($filters['q']))
            ->when(filled($filters['blood_group'] ?? null), fn (Builder $query) => $query->where('blood_group', $filters['blood_group']))
            ->when(filled($filters['country'] ?? null), fn (Builder $query) => $query->where('country', $filters['country']))
            ->when(filled($filters['state'] ?? null), fn (Builder $query) => $query->where('state', 'like', '%'.$filters['state'].'%'))
            ->when(filled($filters['city'] ?? null), fn (Builder $query) => $query->where('city', 'like', '%'.$filters['city'].'%'))
            ->when(filled($filters['division_id'] ?? null), fn (Builder $query) => $query->where('division_id', $filters['division_id']))
            ->when(filled($filters['district_id'] ?? null), fn (Builder $query) => $query->where('district_id', $filters['district_id']))
            ->when(filled($filters['upazila_id'] ?? null), fn (Builder $query) => $query->where('upazila_id', $filters['upazila_id']))
            ->when(filled($filters['union_id'] ?? null), fn (Builder $query) => $query->where('union_id', $filters['union_id']))
            ->when(array_key_exists('available', $filters) && $filters['available'] !== null, fn (Builder $query) => $query->where('is_available', (bool) $filters['available']));
    }

    public function scopeOrderForDirectory(Builder $query, ?string $sort): Builder
    {
        return match ($sort) {
            'recent' => $query->orderByDesc('last_donation_date'),
            'newest' => $query->latest(),
            default => $query->orderByDesc('rating_avg')->orderByDesc('rating_count')->orderByDesc('verified_at'),
        };
    }

    public function nextAvailableDate(): ?Carbon
    {
        return $this->available_from?->copy();
    }

    public function availableInLabel(): ?string
    {
        if ($this->available_from === null || $this->available_from->isPast()) {
            return null;
        }

        return $this->available_from->diffForHumans(now(), ['parts' => 1, 'short' => true]);
    }

    public function locationLabel(): string
    {
        if (filled($this->state) || filled($this->city)) {
            return collect([
                $this->city,
                $this->state,
                $this->country,
            ])->filter()->unique()->take(3)->implode(', ');
        }

        return collect([
            $this->union?->name,
            $this->upazila?->name,
            $this->district?->name,
            $this->country,
        ])->filter()->unique()->take(4)->implode(', ');
    }

    public function isApproved(): bool
    {
        return $this->status === DonorStatus::Approved;
    }

    public function refreshRatingSummary(): void
    {
        $summary = $this->ratings()
            ->where('status', ReviewStatus::Published)
            ->selectRaw('COUNT(*) as total, AVG(rating) as average')
            ->first();

        $this->forceFill([
            'rating_count' => (int) ($summary->total ?? 0),
            'rating_avg' => round((float) ($summary->average ?? 0), 2),
        ])->saveQuietly();
    }

    public function approve(User $staff): void
    {
        $this->forceFill([
            'status' => DonorStatus::Approved,
            'verified_at' => now(),
            'verified_by' => $staff->id,
            'rejection_reason' => null,
        ])->save();
    }

    public function reject(User $staff, string $reason): void
    {
        $this->forceFill([
            'status' => DonorStatus::Rejected,
            'verified_at' => null,
            'verified_by' => $staff->id,
            'rejection_reason' => $reason,
        ])->save();
    }
}
