<?php

namespace App\Models;

use App\Enums\BloodRequestStatus;
use Database\Factories\BloodRequestFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id', 'patient_name', 'blood_group', 'units', 'hospital',
    'district_id', 'upazila_id', 'contact_phone', 'needed_at', 'note', 'status',
])]
class BloodRequest extends Model
{
    /** @use HasFactory<BloodRequestFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'needed_at' => 'datetime',
            'status' => BloodRequestStatus::class,
            'units' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function upazila(): BelongsTo
    {
        return $this->belongsTo(Upazila::class);
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query->where('status', BloodRequestStatus::Open)
            ->where('needed_at', '>=', now()->subDay());
    }
}
