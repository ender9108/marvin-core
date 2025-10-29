<?php

namespace Marvin\Location\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use Stringable;

final readonly class Humidity implements Stringable
{
    private function __construct(
        public float $value,
    ) {
        Assert::greaterThanEq($value, 0.0, 'LO0012::::zone_humidity_cannot_be_below');
        Assert::lessThanEq($value, 100.0, 'LO0013::::zone_humidity_cannot_be_exceed');
    }

    public static function fromPercentage(float $percentage): self
    {
        return new self($percentage);
    }

    public function toPercentage(): float
    {
        return $this->value;
    }

    public function isComfortable(): bool
    {
        return $this->value >= 30.0 && $this->value <= 60.0;
    }

    public function isTooHigh(): bool
    {
        return $this->value > 70.0;
    }

    public function isTooLow(): bool
    {
        return $this->value < 25.0;
    }

    public function difference(self $other): float
    {
        return abs($this->value - $other->value);
    }

    public function __toString(): string
    {
        return number_format($this->value, 1) . '%';
    }
}
