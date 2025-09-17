<?php

namespace Marvin\Security\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use EnderLab\DddCqrsBundle\Domain\ValueObject\ValueObjectInterface;
use Stringable;

final readonly class Lastname implements ValueObjectInterface, Stringable
{
    private const int MIN = 1;
    private const int MAX = 255;

    public string $value;

    public function __construct(string $lastname)
    {
        Assert::notEmpty($lastname);
        Assert::lengthBetween($lastname, self::MIN, self::MAX);

        $this->value = $lastname;
    }

    public function equals(Lastname $lastname): bool
    {
        return $this->value === $lastname->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
