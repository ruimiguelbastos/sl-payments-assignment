<?php

namespace App;

use App\Util\Date;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $interval_count
 */
class Plan extends Model
{
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function belongsToProduct(Product $product): bool
    {
        return $this->product->id === $product->id;
    }

    public function isBeingBilledInPeriod(Carbon $periodToCalculate, int $trialMonths): bool
    {
        //TODO for year/month/days/weeks
        for($i = $trialMonths ; $i < 12 ; $i += $this->interval_count) {
            if (Date::isSameMonthFromSameYear($periodToCalculate, Carbon::now()->addMonths($i))) {
                return true;
            }
        }

        return false;
    }
}
