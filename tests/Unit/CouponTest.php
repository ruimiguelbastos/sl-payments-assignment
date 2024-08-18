<?php

namespace Tests\Unit;

use App\Coupon;
use App\Money\Money;
use PHPUnit\Framework\TestCase;

class CouponTest extends TestCase
{
    /**
     * @testWith [5, 0, 0]
     *           [5, 5, 0]
     *           [5, 4, 0]
     *           [5, 10, 5]
     */
    public function testAmountOff(float $amountOff, float $price, float $expectedResult): void
    {
        $subject = new Coupon();
        $subject->amount_off = $amountOff;
        
        $result  = $subject->calculateDiscountForPrice(Money::fromFloat($price));
        
        $this->assertEquals($expectedResult, $result->toFloat());
    }
    
/**
     * @testWith [50, 0, 0]
     *           [50, 5, 2.5]
     *           [25, 4, 3]
     *           [30, 10, 7]
     */
    public function testPercentOff(float $percentOff, float $price, float $expectedResult): void
    {
        $subject = new Coupon();
        $subject->percent_off = $percentOff;
        
        $result  = $subject->calculateDiscountForPrice(Money::fromFloat($price));
        
        $this->assertEquals($expectedResult, $result->toFloat());
    }
}
