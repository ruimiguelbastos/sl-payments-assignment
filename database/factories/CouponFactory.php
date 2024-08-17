<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Coupon>
 */
class CouponFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'stripe_id'            => fake()->uuid(),
            'name'                 => fake()->sentence(),
            'amount_off'           => fake()->numberBetween(5, 10),
            'duration_in_months'   => fake()->numberBetween(5, 3),
            'currency'             => 'usd',
        ];
    }
}
