<?php

namespace Database\Factories;

use App\Models\District;
use App\Models\Division;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<District>
 */
class DistrictFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->city();

        return [
            'division_id' => Division::factory(),
            'name' => $name,
            'name_bn' => $name,
            'slug' => Str::slug($name),
        ];
    }
}
