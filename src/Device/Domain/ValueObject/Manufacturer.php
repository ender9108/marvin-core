<?php

namespace Marvin\Device\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use EnderLab\DddCqrsBundle\Domain\ValueObject\ValueObjectTrait;

final readonly class Manufacturer implements \Stringable
{
    use ValueObjectTrait;

    public string $value;

    private function __construct(
        string $value
    ) {
        Assert::notEmpty($value, 'device.exceptions.DE0031.manufacturer_empty');
        Assert::length($value, 100, 'device.exceptions.DE0032.manufacturer_length');

        $this->value = $value;
    }

    public static function fromString(?string $value): self
    {
        return new self($value);
    }

    public function __toString(): string
    {
        return $this->value;
    }
}

