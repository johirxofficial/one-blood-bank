<?php

namespace Database\Factories;

use App\Models\Union;
use App\Models\Upazila;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Union>
 */
class UnionFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->streetName();

        return [
            'upazila_id' => Upazila::factory(),
            'name' => $name,
            'name_bn' => $name,
            'slug' => Str::slug($name),
        ];
    }
}
