<?php

namespace Marvin\Shared\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use EnderLab\DddCqrsBundle\Domain\ValueObject\ValueObjectInterface;
use Stringable;

final readonly class Reference implements ValueObjectInterface, Stringable
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

    public function equals(Reference $reference): bool
    {
        return $this->value === $reference->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
