<?php

namespace Database\Factories;

use App\Models\Donor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Donor>
 */
class DonationFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'donor_id' => Donor::factory(),
            'donated_at' => fake()->dateTimeBetween('-3 years', 'now'),
            'hospital' => fake()->company().' Hospital',
            'note' => fake()->optional()->sentence(),
            'created_by' => null,
        ];
    }
}
