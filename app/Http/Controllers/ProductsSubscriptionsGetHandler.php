<?php

namespace App\Http\Controllers;

use App\Calculators\SubscriptionCalculator;
use App\Clients\StripeBaseClient;
use App\Product;
use App\Subscription;
use App\Transformers\ProductSubscriptionsTransformer;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ProductsSubscriptionsGetHandler
{
    public function __construct(
        private StripeBaseClient $stripeBaseClient,
        private SubscriptionCalculator $subscriptionCalculator,
        private ProductSubscriptionsTransformer $transformer,
    )
    {
    }
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $subscriptions = Subscription::all();
        $clock         = $this->stripeBaseClient->getTestClock();
        $now           = Carbon::createFromTimestamp($clock->frozen_time);

        $calculatedSubscriptions = $this->subscriptionCalculator
            ->calculateSubscriptionPaymentsForTheNextYear($subscriptions, $now);

        $result = $this->transformer->transform(Product::all(), $calculatedSubscriptions);

        return view("product-subscriptions", ['result' => $result, 'now' => $now]);
    }
}
