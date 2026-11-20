<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Process;
use App\Models\ProcessCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Process>
 */
class ProcessFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'process_category_id' => ProcessCategory::factory(),
            'name' => fake()->unique()->words(3, true),
            'slug' => fake()->unique()->slug(3),
        ];
    }
}
