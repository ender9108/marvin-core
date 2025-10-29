<?php

namespace Marvin\Shared\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use Stringable;

final readonly class Slug implements Stringable
{
    public string $value;

    public function __construct(string $value)
    {
        Assert::notEmpty($value, 'SH0001::slug_does_not_empty');

        $this->value = $value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
