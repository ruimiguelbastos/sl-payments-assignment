<?php

namespace App\Calculators;

use App\Collections\SubscriptionCollection;
use App\Collections\SubscriptionCollectionAggregator;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

final class SubscriptionCalculator
{
    public function calculateSubscriptionPaymentsForTheNextYear(
        Collection $collection,
        Carbon $currentTime,
    ): SubscriptionCollectionAggregator
    {
        $subscriptionCollections = $this->prepareCollections($collection);
        $result                  = [];

        foreach ($subscriptionCollections as $stripeId => $collection) {
            $calculated        = $collection->withCalculatedAmounts($currentTime);
            $result[$stripeId] = $calculated;
        }

        return new SubscriptionCollectionAggregator($result);
    }

    /**
     * @return array<string, SubscriptionCollection>
     */
    private function prepareCollections(Collection $collection): array
    {
        $subscriptionCollections = [];

        foreach ($collection as $subscription) {
            $subscriptionCollections[$subscription->stripe_id] = SubscriptionCollection::createFromSubscription(
                $subscription
            );
        }

        return $subscriptionCollections;
    }
}