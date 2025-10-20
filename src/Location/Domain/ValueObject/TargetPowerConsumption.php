<?php

namespace Marvin\Location\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use EnderLab\DddCqrsBundle\Domain\ValueObject\ValueObjectInterface;

final readonly class TargetPowerConsumption implements ValueObjectInterface
{
    private const string UNIT_W = 'W';

    public float $value;

    public function __construct(float $value)
    {
        Assert::greaterThan($value, 0);
        $this->value = round($value, 2);
    }

    public static function fromFloat(float $value): self
    {
        return new self($value);
    }

    public function toString(): string
    {
        return $this->value . ' ' . self::UNIT_W;
    }

    public function toFloat(): float
    {
        return $this->value;
    }

    public function equals(ValueObjectInterface $other): bool
    {
        return $other instanceof self && abs($this->value - $other->value) < 0.01;
    }
}
