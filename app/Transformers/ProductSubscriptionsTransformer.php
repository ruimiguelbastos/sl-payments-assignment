<?php

namespace App\Transformers;

use App\Aggregators\ProductSubscriptionAggregator;
use App\Collections\SubscriptionCollectionAggregator;
use Illuminate\Database\Eloquent\Collection;

final class ProductSubscriptionsTransformer
{    public function transform(
        Collection $products,
        SubscriptionCollectionAggregator $calculatedSubscriptions
    ): array {
        $transformed = [];
        
        foreach ($products as $product) {
            $transformed[] =  ProductSubscriptionAggregator::createFromProductAndSubscriptionCollectionAggregate(
                $product,
                $calculatedSubscriptions
            )->transform();
        }
        
        return $transformed;
    }
}
