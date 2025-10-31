<?php

namespace Marvin\Location\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use Stringable;

final readonly class PowerConsumption implements Stringable
{
    private const string UNIT_W = 'W';
    private const string UNIT_KW = 'KW';

    public function __construct(
        public ?float $value
    ) {
        if (null !== $this->value) {
            Assert::greaterThan($value, 0, 'location.exceptions.LO0019.power_consumption_cannot_be_negative');
        }
    }

    public static function fromWatts(float $value): self
    {
        return new self($value);
    }

    public function toWatts(): float
    {
        return $this->value;
    }

    public function toKilowatts(): float
    {
        return $this->value / 1000;
    }

    public function exceedsBudget(float $budgetWatts): bool
    {
        return $this->value > $budgetWatts;
    }

    public function __toString(): string
    {
        if ($this->value >= 1000) {
            return number_format($this->toKilowatts(), 2) . ' ' . self::UNIT_KW;
        }

        return number_format($this->value, 0) . ' ' . self::UNIT_W;
    }
}
