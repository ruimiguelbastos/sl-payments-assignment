<?php

namespace App\Http\Controllers;

use App\Calculators\SubscriptionCalculator;
use App\Product;
use App\Subscription;
use App\Transformers\ProductSubscriptionsTransformer;
use Illuminate\Http\Request;

class ProductsSubscriptionsGetHandler
{
    public function __construct(
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

        $calculatedSubscriptions = $this->subscriptionCalculator
            ->calculateSubscriptionPaymentsForTheNextYear($subscriptions);

        $result = $this->transformer->transform(Product::all(), $calculatedSubscriptions);

        return view("product-subscriptions", ['result' => $result]);
    }
}
