<?php

namespace App\Models;

use App\Enums\DonorStatus;
use App\Enums\UserRole;
use App\Notifications\VerifyEmailNotification;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

#[Fillable([
    'name', 'username', 'email', 'phone', 'password', 'role',
    'avatar_path', 'is_active', 'email_verified_at', 'phone_verified_at',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'is_active' => 'boolean',
            'banned_at' => 'datetime',
            'last_login_at' => 'datetime',
        ];
    }

    public function donor(): HasOne
    {
        return $this->hasOne(Donor::class);
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }

    public function bloodRequests(): HasMany
    {
        return $this->hasMany(BloodRequest::class);
    }

    public function sentMessages(): HasMany
    {
        return $this->hasMany(DirectMessage::class, 'sender_id');
    }

    public function receivedMessages(): HasMany
    {
        return $this->hasMany(DirectMessage::class, 'recipient_id');
    }

    public function isStaff(): bool
    {
        return $this->role->isStaff();
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    public function isManager(): bool
    {
        return $this->role === UserRole::Manager;
    }

    public function canManageUsers(): bool
    {
        return $this->role->canManageUsers();
    }

    public function canManageSettings(): bool
    {
        return $this->role->canManageSettings();
    }

    public function isApprovedDonor(): bool
    {
        return $this->donor?->status === DonorStatus::Approved;
    }

    public function profileUrl(): string
    {
        return route('donors.show.username', $this->username);
    }

    public function avatarUrl(): string
    {
        if ($this->avatar_path && Storage::disk('public')->exists($this->avatar_path)) {
            return asset('storage/'.$this->avatar_path);
        }

        return asset('images/default-avatar.svg');
    }

    public function avatarFilePath(): ?string
    {
        if (! $this->avatar_path) {
            return null;
        }

        $path = Storage::disk('public')->path($this->avatar_path);

        return is_file($path) ? $path : null;
    }

    /**
     * Only send verification mail when the administrator requires it. This
     * prevents Laravel's Registered event from mailing every new user when
     * email verification is optional.
     */
    public function sendEmailVerificationNotification(): void
    {
        if (! Setting::emailVerificationRequired()) {
            return;
        }

        $this->notify(new VerifyEmailNotification);
    }

    public function needsPhoneVerification(): bool
    {
        return Setting::phoneVerificationRequired() && $this->phone_verified_at === null;
    }

    public function needsEmailVerification(): bool
    {
        return Setting::emailVerificationRequired() && $this->email_verified_at === null;
    }

    public function initials(): string
    {
        return Str::of($this->name)->explode(' ')->take(2)->map(fn (string $part) => Str::substr($part, 0, 1))->implode('');
    }
}
