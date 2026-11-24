<?php

namespace Database\Factories;

use App\Models\DirectMessage;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DirectMessage>
 */
class DirectMessageFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sender_id' => User::factory()->staff(),
            'recipient_id' => User::factory(),
            'subject' => fake()->sentence(4),
            'body' => fake()->paragraph(),
            'channel' => 'email',
        ];
    }
}
