<?php

namespace App\Models;

use App\Enums\ReviewStatus;
use Database\Factories\RatingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['donor_id', 'user_id', 'rating', 'comment', 'status'])]
class Rating extends Model
{
    /** @use HasFactory<RatingFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'status' => ReviewStatus::class,
        ];
    }

    protected static function booted(): void
    {
        static::saved(fn (Rating $rating) => $rating->donor->refreshRatingSummary());
        static::deleted(fn (Rating $rating) => $rating->donor->refreshRatingSummary());
    }

    public function donor(): BelongsTo
    {
        return $this->belongsTo(Donor::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', ReviewStatus::Published);
    }
}
