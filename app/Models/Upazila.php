<?php

namespace App\Models;

use App\Models\Concerns\HasCountryScope;
use App\Models\Concerns\HasLocalizedName;
use Database\Factories\UpazilaFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['country_code', 'district_id', 'name', 'name_bn', 'slug', 'bbs_code', 'latitude', 'longitude'])]
class Upazila extends Model
{
    /** @use HasFactory<UpazilaFactory> */
    use HasCountryScope, HasFactory, HasLocalizedName;

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function unions(): HasMany
    {
        return $this->hasMany(Union::class);
    }

    public function donors(): HasMany
    {
        return $this->hasMany(Donor::class);
    }
}
