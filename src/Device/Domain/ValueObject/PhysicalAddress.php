<?php

namespace Marvin\Device\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use EnderLab\DddCqrsBundle\Domain\ValueObject\ValueObjectTrait;
use Stringable;

final readonly class PhysicalAddress implements Stringable
{
    use ValueObjectTrait;

    public string $value;

    private function __construct(
        string $value
    ) {
        Assert::notEmpty($value, 'device.exceptions.DE0029.physical_address_empty');
        Assert::length($value, 100, 'device.exceptions.DE0030.physical_address_length');

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

