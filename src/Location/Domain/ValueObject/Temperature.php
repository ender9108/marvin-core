<?php

/**
 * Marvin Core - DDD-based home automation system
 *
 * @package   Marvin\Core
 * @author    Alexandre Berthelot <alexandreberthelot9108@gmail.com>
 * @copyright 2024-present Alexandre Berthelot
 * @license   AGPL-3.0 License
 * @link      https://github.com/ender9108/marvin-core
 */

declare(strict_types=1);

namespace Marvin\Location\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use Stringable;

final readonly class Temperature implements Stringable
{
    public ?float $value;

    private function __construct(
        float $value,
    ) {
        Assert::greaterThanEq($value, -50.0, 'location.exception.LO0015.zone_temperature_cannot_be_below');
        Assert::lessThanEq($value, 100.0, 'location.exceptions.LO0016.zone_temperature_cannot_be_exceed');
        $this->value = round($value, 2);
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
