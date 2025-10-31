<?php

namespace Marvin\Location\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use Stringable;

final readonly class Temperature implements Stringable
{
    private function __construct(
        public ?float $value,
    ) {
        if (null !== $this->value) {
            Assert::greaterThanEq($this->value, -50.0, 'location.exception.LO0015.zone_temperature_cannot_be_below');
            Assert::lessThanEq($this->value, 100.0, 'location.exceptions.LO0016.zone_temperature_cannot_be_exceed');
        }
    }

    public static function fromCelsius(float $celsius): self
    {
        return new self($celsius);
    }

    public function toCelsius(): float
    {
        return $this->value;
    }

    public function toFahrenheit(): float
    {
        return ($this->value * 9 / 5) + 32;
    }

    public function difference(self $other): float
    {
        return abs($this->value - $other->value);
    }

    public function isHigherThan(self $other): bool
    {
        return $this->value > $other->value;
    }

    public function isLowerThan(self $other): bool
    {
        return $this->value < $other->value;
    }

    public function __toString(): string
    {
        return number_format($this->value, 1) . '°C';
    }
}
