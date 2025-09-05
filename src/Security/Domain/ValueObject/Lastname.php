<?php

namespace Marvin\Security\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\Assert;
use Stringable;

final class Lastname implements Stringable
{
    private const int MIN = 1;
    private const int MAX = 255;

    public readonly string $lastname;

    public function __construct(string $lastname) {
        Assert::notEmpty($lastname);
        Assert::lengthBetween($lastname, self::MIN, self::MAX);

        $this->lastname = $lastname;
    }

    public function equals(Lastname $lastname): bool
    {
        return $this->lastname === $lastname->lastname;
    }

    public function __toString(): string
    {
        return $this->lastname;
    }
}
