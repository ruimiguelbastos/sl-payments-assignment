<?php

namespace App\Collections;

use Illuminate\Database\Eloquent\Collection;

final class SubscriptionCollectionAggregator

{
    /** @param array<int, SubscriptionCollection> $collectionsAggregated **/
    public function __construct(private array $collectionsAggregated)
    {
    }
}
