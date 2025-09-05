<?php

namespace Marvin\Shared\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\Assert;
use Stringable;

final class Description implements Stringable
{
    private const int MIN = 1;
    private const int MAX = 5000;

    public readonly string $description;

    public function __construct(string $description) {
        Assert::notEmpty($description);
        Assert::lengthBetween($description, self::MIN, self::MAX);

        $this->description = $description;
    }

    public function equals(Description $description): bool
    {
        return $this->description === $description->description;
    }

    public function __toString(): string
    {
        return $this->description;
    }
}
