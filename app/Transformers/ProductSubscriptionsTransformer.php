<?php

namespace App\Transformers;

use App\Aggregators\ProductSubscriptionAggregator;
use App\Collections\SubscriptionCalculationsCollectionAggregator;
use Illuminate\Database\Eloquent\Collection;

final class ProductSubscriptionsTransformer
{    public function transform(
    Collection $products,
    SubscriptionCalculationsCollectionAggregator $calculatedSubscriptions
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
