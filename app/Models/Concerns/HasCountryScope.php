<?php

namespace App\Models\Concerns;

use App\Support\LocationCountry;
use Illuminate\Database\Eloquent\Builder;

trait HasCountryScope
{
    public function scopeForCountry(Builder $query, ?string $countryCode = null): Builder
    {
        $code = $countryCode ? strtoupper($countryCode) : LocationCountry::code();

        return $query->where($this->getTable().'.country_code', $code);
    }
}
