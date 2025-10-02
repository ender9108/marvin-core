<?php

namespace Marvin\Security\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use EnderLab\DddCqrsBundle\Domain\ValueObject\ValueObjectInterface;
use Stringable;

final readonly class Firstname implements ValueObjectInterface, Stringable
{
    private const int MIN = 1;
    private const int MAX = 255;

    public string $value;

    public function __construct(string $firstname)
    {
        Assert::notEmpty($firstname);
        Assert::lengthBetween($firstname, self::MIN, self::MAX);

        $this->value = $firstname;
    }

    public function equals(Firstname $firstname): bool
    {
        return $this->value === $firstname->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
