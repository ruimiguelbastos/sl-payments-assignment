<?php

namespace App;

use App\Money\Money;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property float|null $amount_off
 * @property float|null $percent_off
 */
class Coupon extends Model
{
    use HasFactory;

    public function calculateDiscountForPrice(Money $price): Money
    {
        if ($price->isZero()) {
            return $price;
        }

        $newPrice = match(true) {
            $this->amount_off !== null =>  $price->sub(Money::fromFloat($this->amount_off)),
            $this->percent_off !== null => $price->sub($price->multiply($this->percent_off / 100)),
            default => $price,
        };

        return Money::max(Money::zero(), $newPrice);
    }
}
