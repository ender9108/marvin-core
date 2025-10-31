<?php

namespace Marvin\Location\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use Stringable;

final readonly class TargetTemperature implements Stringable
{
    private const float MIN_VALUE = -10.0;
    private const float MAX_VALUE = 55.0;
    private const string UNIT_C = '°C';

    public float $value;

    public function __construct(float $value)
    {
        Assert::greaterThan($value, self::MIN_VALUE, 'location.exceptions.LO0024.zone_target_temp_lower_than');
        Assert::lessThan($value, self::MAX_VALUE, 'location.exceptions.LO0025.zone_target_temp_greater_than');
        $this->value = round($value, 2);
    }

    public static function fromFloat(float $value): self
    {
        return new self($value);
    }

    public function __toString(): string
    {
        return $this->value . ' ' . self::UNIT_C;
    }

    public function toFloat(): float
    {
        return $this->value;
    }
}
