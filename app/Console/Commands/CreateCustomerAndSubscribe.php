<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Stripe\Coupon;
use Stripe\Price;
use Stripe\Subscription;
use Stripe\StripeClient;

class CreateCustomerAndSubscribe extends Command
{
    private const GBP_CURRENCY = 'gbp';
    
    private const COUPON_NAME              = '5 Dollar Off for 3 Months';
    private const PRICE_BASIC_LOOKUP_KEY   = 'monthly_crossclip_basic';
    private const PRICE_PREMIUM_LOOKUP_KEY = 'monthly_crossclip_premium';
    
    public function __construct(private StripeClient $stripeClient)
    {
        parent::__construct();
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-customer-and-subscribe';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $priceBasic   = null;
        $pricePremium = null;
        
        foreach ($this->stripeClient->prices->all(['lookup_keys' => [self::PRICE_BASIC_LOOKUP_KEY, self::PRICE_PREMIUM_LOOKUP_KEY]]) as $price) {
            if($price->lookup_key === self::PRICE_BASIC_LOOKUP_KEY) {
                $priceBasic = $price;
                continue;
            }
            if($price->lookup_key === self::PRICE_PREMIUM_LOOKUP_KEY) {
                $pricePremium = $price;
                continue;
            }
        }
        
        assert($priceBasic instanceof Price);
        assert($pricePremium instanceof Price);

        //Couldn't find a better way to look for the coupon other than by name
        $fiveOffcoupon = null;
        foreach ($this->stripeClient->coupons->all() as $coupon) {
            if($coupon->name !== self::COUPON_NAME) {
                continue;
            }
            
            $fiveOffcoupon = $coupon;
            break;
        }

        assert($fiveOffcoupon instanceof Coupon);

        $customer     = $this->stripeClient->customers->create([
            'test_clock' => env('STRIPE_TEST_CLOCK'),
            'name'       => 'New Customer',
            'email'      => 'newcustomer@example.com',
          ]);
        $subscription = $this->stripeClient->subscriptions->create([
            'customer'          => $customer->id,
            'currency'          => self::GBP_CURRENCY,
            'coupon'            => $coupon->id,
            'trial_period_days' => 30,
            'items'             => [['price' => $priceBasic->id]],
        ]);

        //$this->upgradeSubscription($subscription, $priceBasic, $pricePremium); --Not working
    }

    private function upgradeSubscription(Subscription $subscription, Price $priceBasic, Price $pricePremium): void
    {
        $testClock      = $this->stripeClient->testHelpers->testClocks->retrieve(env('STRIPE_TEST_CLOCK'));
        $protrationDate = Carbon::createFromTimestamp($testClock->frozen_time)->addMonths(5)->day(15);

        $this->stripeClient->subscriptionSchedules->create([
            'from_subscription' => $subscription->id,
            'phases'     => [
                [
                    'items'    => [
                        ['price' => $priceBasic->id],
                    ],
                    'end_date' => $protrationDate->timestamp,
                ],
                [
                    'items' => [
                        ['price' => $pricePremium->id],
                    ],
                ],
            ],
        ]);
    }
}
