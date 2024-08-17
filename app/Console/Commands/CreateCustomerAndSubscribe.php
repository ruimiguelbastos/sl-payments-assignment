<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Stripe\Coupon;
use Stripe\Price;
use Stripe\StripeClient;

class CreateCustomerAndSubscribe extends Command
{
    private const GBP_CURRENCY = 'gbp';
    
    private const COUPON_NAME              = '5 Dollar Off for 3 Months';
    private const PRICE_BASIC_LOOKUP_KEY   = 'monthly_crossclip_premium';
    private const PRICE_PREMIUM_LOOKUP_KEY = 'monthly_crossclip_basic';
    
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
        
        foreach ($this->stripeClient->prices->all() as $price) {
            if($price->lookup_key === self::PRICE_BASIC_LOOKUP_KEY) {
                $priceBasic = $price;
                continue;
            }
            if($price->lookup_key === self::PRICE_PREMIUM_LOOKUP_KEY) {
                $pricePremium = $price;
                continue;
            }
        }
        
        $fiveOffcoupon = null;
        foreach ($this->stripeClient->coupons->all() as $coupon) {
            if($coupon->name !== self::COUPON_NAME) {
                continue;
            }
            
            $fiveOffcoupon = $coupon;
            break;
        }
        
        assert($fiveOffcoupon instanceof Coupon);
        
        $customer = $this->stripeClient->customers->create([
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
    }
}
