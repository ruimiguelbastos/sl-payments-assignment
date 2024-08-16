<?php

namespace App\Calculators;

use App\Collections\SubscriptionCollection;
use App\Collections\SubscriptionCollectionAggregator;
use Carbon\Carbon;

final class SubscriptionCalculator
{
    public function calculateSubscriptionPaymentsForTheNextYear(
        SubscriptionCollection $collection
    ): SubscriptionCollectionAggregator {
        $result = [];
        for ($month = 0 ; $month < 12; $month++) {
            $periodToCalculate = Carbon::now()->startOfMonth()->addMonths($month)->endOfMonth();
            $calculated        = $collection->withCalculatedAmountsForPeriod($periodToCalculate);

            $result[$month] = $calculated;
        }

        return new SubscriptionCollectionAggregator($result);
    }
}