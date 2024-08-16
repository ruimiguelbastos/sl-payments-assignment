<?php

namespace App\Collections;

use App\Product;
use App\Subscription;
use Carbon\Carbon;

final class SubscriptionCollection
{
    /** @param array<int, Subscription> $subscriptions */
    private function __construct(private array $subscriptions)
    {
    }

    public static function createFromSubscription(Subscription $subscription): self
    {
        $collection = [];
        for ($month = 0 ; $month < 12; $month++) {
            $collection[$month] = clone $subscription;
        }

        return new self($collection);
    }

    public function withCalculatedAmounts(): self
    {
        $withCalculatedAmounts = [];
        
        foreach ($this->subscriptions as $month => $subscription) {
            $periodToCalculate = Carbon::now()->addMonths($month)->endOfMonth();
            $withCalculatedAmounts[] = $subscription->calculateAmoutForPeriod($periodToCalculate);
        }
        
        return new self($withCalculatedAmounts);
    }


    public function belongsToProduct(Product $product): bool
    {
        return $this->subscriptions[0]->belongsToProduct($product);
    }

    public function getAllPrices(): array
    {
        return array_map(
            function(Subscription $subscription) { return $subscription->getCalculatedPrice()->toFloat(); },
            $this->subscriptions
        );
    }

    public function transform(): object
    {
        $prices = $this->getAllPrices();
        
        return (object) [
            'prices'        => $prices,
            'totalPrice'    => array_sum($prices),
            'customerEmail' => $this->subscriptions[0]->getCustomerEmail(),
        ];
    }
}
