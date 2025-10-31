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
        Assert::notEmpty($label, 'shared.exceptions.SH0011.label_does_not_empty');
        Assert::lengthBetween($label, self::MIN, self::MAX, 'shared.exceptions.SH0012.label_length_between');

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
