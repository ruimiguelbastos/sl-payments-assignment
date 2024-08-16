<?php

namespace App;

use function round;

final class Money
{
    private const PRECISION = 100;

    private function __construct(private int $integerValue)
    {
    }

    /** @psalm-pure */
    public static function zero(): self
    {
        return new self(0);
    }

    /** @psalm-pure */
    public static function fromFloat(float $value): self
    {
        return new self($value * self::PRECISION);
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

    /** @psalm-pure */
    public static function sum(self ...$addends): self
    {
        $sum = 0;
        foreach ($addends as $money) {
            $sum += $money->integerValue;
        }

        return new self($sum);
    }
}
