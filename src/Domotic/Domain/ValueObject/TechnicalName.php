<?php

namespace Marvin\Domotic\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use EnderLab\DddCqrsBundle\Domain\ValueObject\ValueObjectInterface;
use Stringable;

final readonly class TechnicalName implements ValueObjectInterface, Stringable
{
    private const int MAX = 255;

    public string $value;

    public function __construct(string $technicalName)
    {
        Assert::length($technicalName, self::MAX);

        $this->value = $technicalName;
    }

    public function equals(TechnicalName $technicalName): bool
    {
        return $this->value === $technicalName->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
