<?php

namespace App;

use App\Money\Money;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property Carbon $start
 * @property Carbon $end
 */
class Discount extends Model
{
    use HasFactory;

    protected $casts = [
        'start' => 'date',
        'end'   => 'date',
    ];

    public function coupon(): BelongsTo
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
