<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['donor_id', 'label', 'phone'])]
class DonorContact extends Model
{
    public function donor(): BelongsTo
    {
        return $this->belongsTo(Donor::class);
    }

    public function displayLabel(): string
    {
        return filled($this->label) ? $this->label : 'Other';
    }
}
