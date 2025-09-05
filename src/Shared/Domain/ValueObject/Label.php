<?php

namespace Marvin\Shared\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\Assert;
use Stringable;

final class Label implements Stringable
{
    private const int MIN = 5;
    private const int MAX = 255;

    public readonly string $label;

    public function __construct(string $label) {
        Assert::notEmpty($label);
        Assert::lengthBetween($label, self::MIN, self::MAX);

        $this->label = $label;
    }

    public function equals(Label $label): bool
    {
        return $this->label === $label->label;
    }

    public function __toString(): string
    {
        return $this->label;
    }
}
