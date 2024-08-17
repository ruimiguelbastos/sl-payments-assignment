<?php

namespace Database\Factories;

use App\Coupon;
use App\Customer;
use App\Subscription;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Discount>
 */
class DiscountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'stripe_id'       => fake()->uuid(),
            'subscription_id' => Subscription::factory(),
            'customer_id'     => Customer::factory(),
            'coupon_id'       => Coupon::factory(),
            'start'           => fake()->date(),
            'end'             => fake()->date(),
        ];
    }
}
