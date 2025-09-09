<?php

namespace Marvin\Shared\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\Assert;
use EnderLab\DddCqrsBundle\Domain\ValueObject\ValueObjectInterface;
use Stringable;

final class Description implements ValueObjectInterface, Stringable
{
    private const int MIN = 1;
    private const int MAX = 5000;

    public readonly string $value;

    public function __construct(string $description) {
        Assert::notEmpty($description);
        Assert::lengthBetween($description, self::MIN, self::MAX);

        $this->value = $description;
    }

    public function equals(Description $description): bool
    {
        return $this->value === $description->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
