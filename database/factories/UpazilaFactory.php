<?php

namespace Database\Factories;

use App\Models\District;
use App\Models\Upazila;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Upazila>
 */
class UpazilaFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->city();

        return [
            'district_id' => District::factory(),
            'name' => $name,
            'name_bn' => $name,
            'slug' => Str::slug($name),
        ];
    }
}
