<?php

namespace App\Collections;

use App\Product;
use App\Subscription;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

final class SubscriptionCollection
{
    /** @param Subscription[] $subscriptions */
    private function __construct(private array $subscriptions)
    {
    }
    
    public static function createFromCollection(Collection $subscriptions)
    {
        return new self($subscriptions->all());
    }

    public function withCalculatedAmountsForPeriod(Carbon $periodToCalculate): self
    {
        $withCalculatedAmounts = [];
        
        foreach ($this->subscriptions as $subscription) {
            $withCalculatedAmounts[] = $subscription->calculateAmoutForPeriod($periodToCalculate);
        }
        
        return new self($withCalculatedAmounts);
    }
    
    /**
     * @return Subscription[]
     */
    public function toArray(): array
    {
        return $this->subscriptions;
    }
    
    public function filterByProduct(Product $product): self
    {
        $result = [];
        
        foreach ($this->subscriptions as $subscription) {
            if (! $subscription->belongsToProduct($product)) {
                continue;
            }
            
            $result[] = $subscription;
        }
        
        return new self($result);
    }
}
