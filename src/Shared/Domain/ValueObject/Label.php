<?php

namespace Marvin\Shared\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use EnderLab\DddCqrsBundle\Domain\ValueObject\ValueObjectInterface;
use Stringable;

final readonly class Label implements ValueObjectInterface, Stringable
{
    private const int MIN = 2;
    private const int MAX = 255;

    public string $value;

    public function __construct(string $label)
    {
        Assert::notEmpty($label);
        Assert::lengthBetween($label, self::MIN, self::MAX);

        $this->value = $label;
    }

    public function equals(Label $label): bool
    {
        return $this->value === $label->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
