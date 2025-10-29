<?php

namespace Marvin\Shared\Domain\ValueObject;

use Stringable;

final readonly class Slug implements Stringable
{
    public function __construct(public string $value)
    {
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}
