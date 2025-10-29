<?php

namespace Marvin\Location\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use Stringable;

final readonly class SurfaceArea implements Stringable
{
    private const float MIN_VALUE = 0.0;
    private const float MAX_VALUE = 100000.0;
    private const string UNIT = 'm²';

    public float $value;

    public function __construct(float $value)
    {
        Assert::greaterThan($value, self::MIN_VALUE, 'LO0015::::surface_area_cannot_be_negative');
        Assert::lessThan($value, self::MAX_VALUE, 'LO0016::::surface_area_cannot_exceed_100000');
        $this->value = round($value, 2);
    }

    public static function fromFloat(float $value): self
    {
        return new self($value);
    }

    public function __toString(): string
    {
        return $this->value . ' ' . self::UNIT;
    }

    public function toFloat(): float
    {
        return $this->value;
    }

    public function equals(self $other): bool
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
