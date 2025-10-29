<?php

namespace Marvin\Shared\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use Stringable;

final readonly class Label implements Stringable
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

    public static function fromString(string $label): self
    {
        return new self($label);
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
