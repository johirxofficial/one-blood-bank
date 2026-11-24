<?php

namespace Database\Factories;

use App\Enums\ReviewStatus;
use App\Models\Donor;
use App\Models\Rating;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Rating>
 */
class RatingFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'donor_id' => Donor::factory(),
            'user_id' => User::factory(),
            'rating' => fake()->numberBetween(3, 5),
            'comment' => fake()->optional()->sentence(),
            'status' => ReviewStatus::Published,
        ];
    }
}
