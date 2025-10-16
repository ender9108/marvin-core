<?php

namespace Marvin\System\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use EnderLab\DddCqrsBundle\Domain\ValueObject\ValueObjectInterface;

final readonly class ContainerImage implements ValueObjectInterface
{
    public string $value;

    public function __construct(string $value)
    {
        Assert::notEmpty($value);
        Assert::regex($value, '/^[a-z0-9\/\-_]+:[a-z0-9\.\-_]+$/i', );

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

    public function getName(): string
    {
        return explode(':', $this->value)[0];
    }

    public function getTag(): string
    {
        return explode(':', $this->value)[1];
    }
}
