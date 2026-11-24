<?php

namespace Database\Factories;

use App\Enums\BloodGroup;
use App\Enums\BloodRequestStatus;
use App\Models\BloodRequest;
use App\Models\District;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BloodRequest>
 */
class BloodRequestFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'patient_name' => fake()->name(),
            'blood_group' => fake()->randomElement(BloodGroup::values()),
            'units' => fake()->numberBetween(1, 4),
            'hospital' => fake()->company().' Hospital',
            'district_id' => District::query()->inRandomOrder()->value('id'),
            'contact_phone' => '01'.fake()->numerify('#########'),
            'needed_at' => fake()->dateTimeBetween('now', '+5 days'),
            'note' => fake()->optional()->sentence(),
            'status' => BloodRequestStatus::Open,
        ];
    }
}
