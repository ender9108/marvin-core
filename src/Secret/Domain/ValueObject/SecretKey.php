<?php

namespace Marvin\Secret\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use EnderLab\DddCqrsBundle\Domain\ValueObject\ValueObjectInterface;
use Stringable;

final readonly class SecretKey implements ValueObjectInterface, Stringable
{
    public function __construct(public string $value)
    {
        Assert::notEmpty($value);
        Assert::regex($value, '/^[a-zA-Z0-9_.:-]{3,128}$/');
    }

    public function equals(ValueObjectInterface $other): bool
    {
        return $other instanceof self && $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
