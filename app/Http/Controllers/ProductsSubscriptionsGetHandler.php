<?php

namespace App\Http\Controllers;

use App\Calculators\SubscriptionCalculator;
use App\Subscription;
use Illuminate\Http\Request;

class ProductsSubscriptionsGetHandler
{
    public function __construct(
        private SubscriptionCalculator $subscriptionCalculator,
    )
    {
    }
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $subscriptions = Subscription::all();
        
        $calculatedSubscriptions = $this->subscriptionCalculator
            ->calculateSubscriptionPaymentsForTheNextYear($subscriptions);
        
        //TODO Transform
    }
}
