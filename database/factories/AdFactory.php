<?php

namespace Database\Factories;

use App\Enums\AdPlacement;
use App\Enums\AdType;
use App\Models\Ad;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ad>
 */
class AdFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->company(),
            'type' => AdType::Banner,
            'placement' => AdPlacement::Sidebar,
            'target_url' => fake()->url(),
            'code' => null,
            'is_active' => true,
            'sort_order' => 0,
        ];
    }
}
