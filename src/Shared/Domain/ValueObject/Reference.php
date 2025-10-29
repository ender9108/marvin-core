<?php

namespace Marvin\Shared\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use Stringable;

final readonly class Reference implements Stringable
{
    private const int MIN = 3;
    private const int MAX = 64;

    public string $value;

    public function __construct(string $reference)
    {
        Assert::notEmpty($reference);
        Assert::lengthBetween($reference, self::MIN, self::MAX);

        $this->value = $reference;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
