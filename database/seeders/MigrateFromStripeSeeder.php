<?php

namespace Database\Seeders;

use App\Clients\StripeBaseClient;
use App\Coupon;
use App\Customer;
use App\Discount;
use App\Plan;
use App\Product;
use App\Subscription;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Stripe\Discount as StripeDiscount;

class MigrateFromStripeSeeder extends Seeder
{
    public function __construct(private StripeBaseClient $client)
    {
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products  = $this->saveProducts();
        $customers = $this->saveCustomers();
        $this->saveSubscriptions($products, $customers);
    }

    private function saveProducts(): array
    {
        $stripeProducts = $this->client->getProducts();
        $products = [];

        foreach ($stripeProducts as $stripeProduct) {
            $products[$stripeProduct->id] = Product::create([
                'name'      => $stripeProduct->name,
                'stripe_id' => $stripeProduct->id,
            ]);
        }

        return $products;
    }

    private function saveCustomers(): array
    {
        $stripeCustomers = $this->client->getCustomers();
        $customers       = [];

        foreach ($stripeCustomers as $stripeCustomer) {
            $customers[$stripeCustomer->id] = Customer::create([
                'name'      => $stripeCustomer->name,
                'email'     => $stripeCustomer->email,
                'stripe_id' => $stripeCustomer->id,
            ]);
        }

        return $customers;
    }
    
    /**
     * @param array<string, Product> $products
     * @param array<string, Customer> $customers
     */
    private function saveSubscriptions(array $products, array $customers): void
    {
        $stripeSubscriptions = $this->client->getSubscriptions();
        
        /** @var \Stripe\Subscription $stripeSubscription **/
        foreach ($stripeSubscriptions as $stripeSubscription) {
            $stripePlan = $stripeSubscription->plan;
            
            $plan = Plan::firstOrCreate([
                'stripe_id'          => $stripePlan->id,
                'amount'             => $stripePlan->amount / 100,
                'interval'           => $stripePlan->interval,
                'interval_count'     => $stripePlan->interval_count,
                'product_id'         => $products[$stripePlan->product]->id,
            ]);
            
            $trialStart = $stripeSubscription->trial_start === null
                ? null
                : Carbon::createFromTimestamp($stripeSubscription->trial_start);
            $trialEnd   = $stripeSubscription->trial_end === null
                ? null
                : Carbon::createFromTimestamp($stripeSubscription->trial_end);
            $cancelAt   = $stripeSubscription->cancel_at === null
                ? null
                : Carbon::createFromTimestamp($stripeSubscription->cancel_at);
            
            $customerId   = $customers[$stripeSubscription->customer]->id;
            $subscription = Subscription::create([
                'stripe_id'            => $stripeSubscription->id,
                'start_date'           => Carbon::createFromTimestamp($stripeSubscription->start_date),
                'current_period_start' => Carbon::createFromTimestamp($stripeSubscription->current_period_start),
                'current_period_end'   => Carbon::createFromTimestamp($stripeSubscription->current_period_end),
                'trial_start'          => $trialStart,
                'trial_end'            => $trialEnd,
                'cancel_at'            => $cancelAt,
                'currency'             => $stripeSubscription->currency,
                'customer_id'          => $customers[$stripeSubscription->customer]->id,
                'plan_id'              => $plan->id,
            ]);
            
            if ($stripeSubscription->discount !== null) {
                $this->saveDiscounts($stripeSubscription, $subscription->id, $customerId);
            }
        }
    }

    private function saveDiscounts(\Stripe\Subscription $stripeSubscription, int $subscriptionId, int $customerId)
    {
        $stripeDiscount = $stripeSubscription->discount;
        
        assert($stripeDiscount instanceof StripeDiscount);
        
        $stripeCoupon = $stripeDiscount->coupon;
        $coupon       = Coupon::firstOrCreate([
            'stripe_id'          => $stripeCoupon->id,
            'name'               => $stripeCoupon->name,
            'amount_off'         => $stripeCoupon->amount_off / 100,
            'percent_off'        => $stripeCoupon->percent_off,
            'currency'           => $stripeCoupon->currency,
            'duration_in_months' => $stripeCoupon->duration_in_months,
        ]);
        
        Discount::create([
            'stripe_id'       => $stripeDiscount->id,
            'customer_id'     => $customerId,
            'subscription_id' => $subscriptionId,
            'coupon_id'       => $coupon->id,
            'start'           => Carbon::createFromTimestamp($stripeDiscount->start),
            'end'             => Carbon::createFromTimestamp($stripeDiscount->end),
        ]);
    }
}
