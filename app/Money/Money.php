<?php

namespace App\Money;

use function round;

final class Money
{
    private const PRECISION = 100;

    private function __construct(private int $integerValue)
    {
    }

    public static function zero(): self
    {
        return new self(0);
    }

    public static function fromFloat(float $value): self
    {
        return new self($value * self::PRECISION);
    }

    public static function max(Money $moneyA, Money $moneyB): self
    {
        return new self(max($moneyA->integerValue, $moneyB->integerValue));
    }

    public function toFloat(): float
    {
        return $this->integerValue / self::PRECISION;
    }

    public function add(Money $money): Money
    {
        return new self($this->integerValue + $money->integerValue);
    }

    public function sub(Money $money): Money
    {
        return new self($this->integerValue - $money->integerValue);
    }

    public function multiply(float $factor): Money
    {
        return new self((int) round((float) $this->integerValue * $factor));
    }

    public function isZero(): bool
    {
        return $this->integerValue === 0;
    }
}
