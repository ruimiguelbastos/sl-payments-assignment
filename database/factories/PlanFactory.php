<?php

namespace Database\Factories;

use App\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Plan>
 */
class PlanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'stripe_id'      => fake()->uuid(),
            'product_id'     => Product::factory(),
            'interval_count' => fake()->numberBetween(1,6),
            'interval'       => 'month',
            'currency'       => 'usd',
            'amount'         => 1000,
        ];
    }
}
