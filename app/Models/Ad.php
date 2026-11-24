<?php

namespace App\Models;

use App\Enums\AdPlacement;
use App\Enums\AdType;
use Database\Factories\AdFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'title', 'type', 'placement', 'image_path', 'target_url', 'code',
    'is_active', 'starts_at', 'ends_at', 'sort_order',
])]
class Ad extends Model
{
    /** @use HasFactory<AdFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'type' => AdType::class,
            'placement' => AdPlacement::class,
            'is_active' => 'boolean',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    public function scopeRunning(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where(fn (Builder $query) => $query->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
            ->where(fn (Builder $query) => $query->whereNull('ends_at')->orWhere('ends_at', '>=', now()));
    }

    public function scopeForPlacement(Builder $query, AdPlacement $placement): Builder
    {
        return $query->where('placement', $placement)->orderBy('sort_order');
    }
}
