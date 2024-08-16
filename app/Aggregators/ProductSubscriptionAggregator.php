<?php

namespace App\Aggregators;

use App\Collections\SubscriptionCollectionAggregator;
use App\Product;

final class ProductSubscriptionAggregator

{
    private function __construct(private Product $product, private SubscriptionCollectionAggregator $subscriptions)
    {
    }

    public static function createFromProductAndSubscriptionCollectionAggregate(
        Product $product,
        SubscriptionCollectionAggregator $subscriptionCollectionAggregator
    ): self {
        return new self($product, $subscriptionCollectionAggregator->filterForProduct($product));
    }
    
    public function transform(): object
    {
        return (object) [
            'productName'   => $this->product->name,
            'subscriptions' => $this->subscriptions->transform(),
        ];
    }
}
