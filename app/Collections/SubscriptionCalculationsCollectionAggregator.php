<?php

namespace App\Collections;

use App\Product;

final class SubscriptionCalculationsCollectionAggregator

{
    /** @param array<string, SubscriptionCalculationsCollection> $collectionsAggregated **/
    public function __construct(private array $collectionsAggregated)
    {
    }

    public function filterForProduct(Product $product): SubscriptionCalculationsCollectionAggregator
    {
        $filtered = [];

        foreach ($this->collectionsAggregated as $stripeId => $collection) {
            if (! $collection->belongsToProduct($product)) {
                continue;
            }

            $filtered[$stripeId] = $collection;
        }

        return new self($filtered);
    }

    public function transform(): object
    {
        $transformedSubscriptions = [];
        $prices      = array_fill(0, 12, 0);

        foreach ($this->collectionsAggregated as $stripeId => $collection) {
            $prices = array_map(
                function () { return array_sum(func_get_args()); },
                $prices,
                $collection->getAllPrices(),
            );

            $result = $collection->transform();

            $transformedSubscriptions[$stripeId] = $result;
        }

        return (object) [
            'data'         => $transformedSubscriptions,
            'monthsTotals' => $prices,
            'yearTotals'   => array_sum($prices)
        ];
    }
}
