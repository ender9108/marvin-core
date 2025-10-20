<?php

namespace Marvin\Location\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use EnderLab\DddCqrsBundle\Domain\ValueObject\ValueObjectInterface;

final readonly class SurfaceArea implements ValueObjectInterface
{
    private const float MIN_VALUE = 0.0;
    private const float MAX_VALUE = 100000.0;
    private const string UNIT = 'm²';

    public float $value;

    public function __construct(float $value)
    {
        Assert::greaterThan($value, self::MIN_VALUE);
        Assert::lessThan($value, self::MAX_VALUE);
        $this->value = round($value, 2);
    }

    public static function fromFloat(float $value): self
    {
        return new self($value);
    }

    public function toString(): string
    {
        return $this->value . ' ' . self::UNIT;
    }

    public function toFloat(): float
    {
        return $this->value;
    }

    public function equals(ValueObjectInterface $other): bool
    {
        return $other instanceof self && abs($this->value - $other->value) < 0.01;
    }

    public function isSmall(): bool
    {
        return $this->value < 10.0;
    }

    public function isMedium(): bool
    {
        return $this->value >= 10.0 && $this->value < 30.0;
    }

    public function isLarge(): bool
    {
        return $this->value >= 30.0;
    }
}
