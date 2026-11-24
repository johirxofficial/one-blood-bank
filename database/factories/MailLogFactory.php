<?php

namespace Database\Factories;

use App\Models\MailLog;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MailLog>
 */
class MailLogFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'to_email' => fake()->safeEmail(),
            'subject' => fake()->sentence(4),
            'template' => 'welcome',
            'status' => 'sent',
            'sent_at' => now(),
        ];
    }
}
