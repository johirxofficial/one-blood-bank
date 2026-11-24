<?php

namespace Database\Factories;

use App\Enums\VerificationPurpose;
use App\Models\PhoneVerification;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends Factory<PhoneVerification>
 */
class PhoneVerificationFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'phone' => '01'.fake()->numerify('#########'),
            'purpose' => VerificationPurpose::Register,
            'code_hash' => Hash::make('123456'),
            'attempts' => 0,
            'expires_at' => now()->addMinutes(10),
        ];
    }
}
