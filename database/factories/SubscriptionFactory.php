<?php

namespace Database\Factories;

use App\Customer;
use App\Plan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Subscription>
 */
class SubscriptionFactory extends Factory
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
            'plan_id'              => Plan::factory(),
            'customer_id'          => Customer::factory(),
            'current_period_start' => fake()->date(),
            'current_period_end'   => fake()->date(),
            'currency'             => 'usd',
        ];
    }
}
