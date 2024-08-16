<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $id
 * @property Carbon $start
 * @property Carbon $end
 */
class Discount extends Model
{
    public function coupon(): HasOne
    {
        return $this->belongsTo(Coupon::class);
    }

    public function calculateForPeriodAndPrice(Carbon $periodToCalculate, Money $price): Money
    {
        $discountStartDate = $this->start;
        $discountEndDate   = $this->end;

        if (! $periodToCalculate->greaterThanOrEqualTo($discountStartDate) ||
            ! $periodToCalculate->lessThanOrEqualTo($discountEndDate)
        ) {
            return $price;
        }

        return $this->coupon->calculateDiscountForPrice($price);
    }
}
