<?php

namespace App\Aggregators;

use App\Collections\SubscriptionCalculationsCollectionAggregator;
use App\Product;

final class ProductSubscriptionAggregator

{
    private function __construct(private Product $product, private SubscriptionCalculationsCollectionAggregator $subscriptions)
    {
    }

    public static function createFromProductAndSubscriptionCollectionAggregate(
        Product $product,
        SubscriptionCalculationsCollectionAggregator $subscriptionCollectionAggregator
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
