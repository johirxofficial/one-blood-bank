<?php

namespace Database\Factories;

use App\Enums\BloodGroup;
use App\Enums\DonorStatus;
use App\Models\District;
use App\Models\Donor;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Donor>
 */
class DonorFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'blood_group' => fake()->randomElement(BloodGroup::values()),
            'village' => fake()->streetName(),
            'address_details' => fake()->address(),
            'date_of_birth' => fake()->dateTimeBetween('-50 years', '-18 years'),
            'gender' => fake()->randomElement(['male', 'female']),
            'weight_kg' => fake()->numberBetween(50, 85),
            'last_donation_date' => fake()->dateTimeBetween('-2 years', 'now'),
            'status' => DonorStatus::Approved,
            'verified_at' => now(),
            'total_donations' => fake()->numberBetween(0, 12),
            'is_public' => true,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => DonorStatus::Pending,
            'verified_at' => null,
        ]);
    }

    public function withLocation(): static
    {
        return $this->state(function (array $attributes): array {
            $district = District::query()->with('division')->inRandomOrder()->first();

            if ($district === null) {
                return [];
            }

            return [
                'division_id' => $district->division_id,
                'district_id' => $district->id,
                'upazila_id' => $district->upazilas()->inRandomOrder()->value('id'),
                'union_id' => null,
            ];
        });
    }
}
