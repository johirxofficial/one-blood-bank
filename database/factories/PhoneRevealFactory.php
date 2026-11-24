<?php

namespace Database\Factories;

use App\Models\Donor;
use App\Models\PhoneReveal;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PhoneReveal>
 */
class PhoneRevealFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'donor_id' => Donor::factory(),
            'ip' => fake()->ipv4(),
            'created_at' => now(),
        ];
    }
}
