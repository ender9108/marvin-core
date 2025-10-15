<?php

namespace Marvin\System\Domain\ValueObject;

use Enderlab\DddCqrsBundle\Domain\Assert\Assert;
use EnderLab\DddCqrsBundle\Domain\ValueObject\ValueObjectInterface;

final readonly class SupervisorProcess implements ValueObjectInterface
{
    private string $value;

    public function __construct(string $value)
    {
        Assert::notEmpty($value);
        Assert::regex($value, '/^[a-z0-9_\-]+$/i', );

        $this->value = $value;
    }

    public function toString(): string
    {
        return $this->value;
    }

    public function equals(ValueObjectInterface $other): bool
    {
        return $other instanceof self && $this->value === $other->value;
    }
}
