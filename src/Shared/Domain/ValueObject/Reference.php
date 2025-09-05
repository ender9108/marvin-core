<?php

namespace Marvin\Shared\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\Assert;
use Stringable;

final class Reference implements Stringable
{
    private const int MIN = 3;
    private const int MAX = 64;

    public readonly string $reference;

    public function __construct(string $reference) {
        Assert::notEmpty($reference);
        Assert::lengthBetween($reference, self::MIN, self::MAX);

        $this->reference = $reference;
    }

    public function equals(Reference $reference): bool
    {
        return $this->reference === $reference->reference;
    }

    public function __toString(): string
    {
        return $this->reference;
    }
}
