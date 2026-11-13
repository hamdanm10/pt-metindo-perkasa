<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\WhatsappAgent;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WhatsappAgent>
 */
class WhatsappAgentFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->firstName(),
            'number' => '62812'.fake()->unique()->numerify('#######'),
            'position' => 0,
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }
}
