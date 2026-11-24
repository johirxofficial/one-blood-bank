<?php

namespace Database\Factories;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ActivityLog>
 */
class ActivityLogFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'action' => fake()->randomElement(['donor.approved', 'donor.rejected', 'user.banned']),
            'description' => fake()->sentence(),
            'ip' => fake()->ipv4(),
            'created_at' => now(),
        ];
    }
}
