<?php

namespace App\Collections;

use App\Money\Money;
use App\Product;
use App\Subscription;
use Carbon\Carbon;

final class SubscriptionCalculationsCollection
{
    /** @param array<int, Money> $calculations */
    private function __construct(
        private Subscription $subscription,
        private array $calculations
    ) {
    }

    public static function calculateAmountsForSubscription(Subscription $subscription, Carbon $now): self
    {
        $calculations = [];

        for ($month = 0 ; $month < 12; $month++) {
            $periodToCalculate    = $now->clone()->addMonths($month)->endOfMonth();
            $calculations[$month] = $subscription->calculateAmoutForPeriod($periodToCalculate);
        }
        
        return new self($subscription, $calculations);
    }


    public function belongsToProduct(Product $product): bool
    {
        return $this->subscription->belongsToProduct($product);
    }

    public function getAllPrices(): array
    {
        return array_map(
            function(Money $price) { return $price->toFloat(); },
            $this->calculations
        );
    }

    public function transform(): object
    {
        $prices = $this->getAllPrices();
        
        return (object) [
            'prices'        => $prices,
            'totalPrice'    => array_sum($prices),
            'customerEmail' => $this->subscription->getCustomerEmail(),
        ];
    }
}
