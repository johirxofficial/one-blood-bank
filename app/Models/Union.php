<?php

namespace App\Models;

use App\Models\Concerns\HasCountryScope;
use App\Models\Concerns\HasLocalizedName;
use Database\Factories\UnionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['country_code', 'upazila_id', 'name', 'name_bn', 'slug', 'bbs_code', 'latitude', 'longitude'])]
class Union extends Model
{
    /** @use HasFactory<UnionFactory> */
    use HasCountryScope, HasFactory, HasLocalizedName;

    public function upazila(): BelongsTo
    {
        return $this->belongsTo(Upazila::class);
    }

    public function donors(): HasMany
    {
        return $this->hasMany(Donor::class);
    }
}
