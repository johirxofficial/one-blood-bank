<?php

namespace App\Models;

use App\Models\Concerns\HasCountryScope;
use App\Models\Concerns\HasLocalizedName;
use Database\Factories\DistrictFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['country_code', 'division_id', 'name', 'name_bn', 'slug', 'bbs_code', 'latitude', 'longitude'])]
class District extends Model
{
    /** @use HasFactory<DistrictFactory> */
    use HasCountryScope, HasFactory, HasLocalizedName;

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    public function upazilas(): HasMany
    {
        return $this->hasMany(Upazila::class);
    }

    public function donors(): HasMany
    {
        return $this->hasMany(Donor::class);
    }
}
