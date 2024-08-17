<?php

namespace App\Calculators;

use App\Collections\SubscriptionCalculationsCollection;
use App\Collections\SubscriptionCalculationsCollectionAggregator;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

final class SubscriptionCalculator
{
    public function calculateSubscriptionPaymentsForTheNextYear(
        Collection $collection,
        Carbon $currentTime,
    ): SubscriptionCalculationsCollectionAggregator
    {
        $result                  = [];

        foreach ($collection as $subscription) {
            $result[$subscription->stripe_id] = SubscriptionCalculationsCollection::calculateAmountsForSubscription(
                $subscription,
                $currentTime,
            );
        }

        return new SubscriptionCalculationsCollectionAggregator($result);
    }
}