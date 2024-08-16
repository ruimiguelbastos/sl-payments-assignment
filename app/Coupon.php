<?php

namespace App;

use App\Money\Money;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property float|null $amount_off
 * @property float|null $percent_off
 */
class Coupon extends Model
{
    public function calculateDiscountForPrice(Money $price): Money
    {
        return match(true) {
            $this->amount_off !== null =>  $price->sub(Money::fromFloat($this->amount_off)),
            $this->percent_off !== null => $price->sub($price->multiply($this->percent_off / 100)),
            default => $price,
        };
    }
}
