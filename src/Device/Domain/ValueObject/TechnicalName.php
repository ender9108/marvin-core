<?php

namespace Marvin\Device\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use Stringable;

final readonly class TechnicalName implements Stringable
{
    public string $value;

    private function __construct(string $value)
    {
        Assert::notEmpty($value);
        Assert::maxLength($value, 255);

        $this->value = $value;
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
