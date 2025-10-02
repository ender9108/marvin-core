<?php

namespace Marvin\Security\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use EnderLab\DddCqrsBundle\Domain\ValueObject\ValueObjectInterface;
use Stringable;

final readonly class Timezone implements ValueObjectInterface, Stringable
{
    public string $value;

    public function __construct(string $timezone)
    {
        Assert::notEmpty($timezone);
        Assert::isValidTimezone($timezone);

        $this->value = $timezone;
    }

    public function equals(Timezone $timezone): bool
    {
        return $this->value === $timezone->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
