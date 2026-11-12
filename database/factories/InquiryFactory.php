<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Inquiry;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Inquiry>
 */
class InquiryFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'question' => fake()->sentence().'?',
            'answer' => fake()->paragraph(),
            'asked_at' => fake()->dateTimeBetween('-1 year')->format('Y-m-d'),
            'service_id' => null,
            'product_id' => null,
            'created_by' => null,
        ];
    }
}
