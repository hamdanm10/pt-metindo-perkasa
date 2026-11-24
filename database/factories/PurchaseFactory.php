<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Purchase;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Purchase>
 */
class PurchaseFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'buyer_id' => User::factory()->customer(),
            'purchased_at' => fake()->dateTimeBetween('-1 year')->format('Y-m-d'),
            'invoice_number' => null,
            'notes' => null,
            'created_by' => null,
        ];
    }
}
