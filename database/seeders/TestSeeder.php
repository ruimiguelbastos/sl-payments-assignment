<?php

namespace Database\Seeders;

use App\Coupon;
use App\Customer;
use App\Discount;
use App\Plan;
use App\Product;
use App\Subscription;
use Illuminate\Database\Seeder;

class TestSeeder extends Seeder
{
    
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customer1 = Customer::factory(['email' => 'koepp.maye@example.org'])->create();
        $customer2 = Customer::factory(['email' => 'jbeatty@example.org'])->create();
        
        $product1 = Product::factory()->create();
        $product2 = Product::factory()->create();
        $product3 = Product::factory()->create();
        
        $plan1 = Plan::factory(['product_id' => $product1, 'amount' => 55, 'interval_count' => 3])->create();
        $plan2 = Plan::factory(['product_id' => $product2, 'amount' => 90, 'interval_count' => 6])->create();
        $plan3 = Plan::factory(['product_id' => $product3, 'amount' => 20, 'interval_count' => 1])->create();
        
        Subscription::factory([
            'stripe_id'            => 'sub_1',
            'customer_id'          => $customer1,
            'plan_id'              => $plan1,
            'start_date'           => '2024-08-14 17:31:54',
            'current_period_start' => '2024-08-14 17:31:54',
            'current_period_end'   => '2024-11-14 17:31:54',
            'currency'             => 'gbp',
        ])->create();
        Subscription::factory([
            'stripe_id'            => 'sub_2',
            'customer_id'          => $customer1,
            'plan_id'              => $plan2,
            'start_date'           => '2024-08-14 17:31:54',
            'current_period_start' => '2024-08-14 17:31:54',
            'current_period_end'   => '2025-02-14 17:31:54',
            'currency'             => 'gbp'
        ])->create();
        Subscription::factory([
            'stripe_id'            => 'sub_3',
            'customer_id'          => $customer1,
            'plan_id'              => $plan3,
            'start_date'           => '2024-08-14 17:31:54',
            'current_period_start' => '2024-08-14 17:31:54',
            'current_period_end'   => '2025-02-14 17:31:54',
            'cancel_at'            => '2024-09-14 17:31:54',
            'currency'             => 'gbp',
        ])->create();
        Subscription::factory([
            'stripe_id'            => 'sub_4',
            'customer_id'          => $customer2,
            'plan_id'              => $plan1,
            'start_date'           => '2024-08-14 17:31:54',
            'current_period_start' => '2024-08-14 17:31:54',
            'current_period_end'   => '2024-11-14 17:31:54',
            'cancel_at'            => '2024-11-14 17:31:54',
            'currency'             => 'eur',
        ])->create();
        Subscription::factory([
            'stripe_id'            => 'sub_5',
            'customer_id'          => $customer2,
            'plan_id'              => $plan2,
            'start_date'           => '2024-08-14 17:31:54',
            'current_period_start' => '2024-08-14 17:31:54',
            'current_period_end'   => '2024-11-12 17:31:54',
            'trial_start'          => '2024-08-14 17:31:54',
            'trial_end'            => '2024-11-12 17:31:54',
            'currency'             => 'eur',
        ])->create();
        $subscription = Subscription::factory([
            
            'stripe_id'            => 'sub_6',
            'customer_id'          => $customer2,
            'plan_id'              => $plan3,
            'start_date'           => '2024-08-14 17:31:54',
            'current_period_start' => '2024-08-14 17:31:54',
            'current_period_end'   => '2024-09-14 17:31:54',
            'currency'             => 'eur',
        ])->create();
        
        $coupon = Coupon::factory([
            'amount_off'         => 5,
            'duration_in_months' => 3,
        ])->create();
        
        Discount::factory([
            'subscription_id' => $subscription,
            'customer_id'     => $customer2,
            'coupon_id'       => $coupon,
            'start'           => '2024-08-14 17:31:54',
            'end'             => '2024-11-14 17:31:54',
        ])->create();
    }
}
