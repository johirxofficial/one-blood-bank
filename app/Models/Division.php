<?php

namespace App\Models;

use App\Models\Concerns\HasCountryScope;
use App\Models\Concerns\HasLocalizedName;
use Database\Factories\DivisionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['country_code', 'name', 'name_bn', 'slug', 'bbs_code', 'latitude', 'longitude'])]
class Division extends Model
{
    /** @use HasFactory<DivisionFactory> */
    use HasCountryScope, HasFactory, HasLocalizedName;

    public function districts(): HasMany
    {
        return $this->hasMany(District::class);
    }

    public function donors(): HasMany
    {
        return $this->hasMany(Donor::class);
    }
}
