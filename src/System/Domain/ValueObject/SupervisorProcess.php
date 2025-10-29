<?php

namespace Marvin\System\Domain\ValueObject;

use Enderlab\DddCqrsBundle\Domain\Assert\Assert;
use Stringable;

final readonly class SupervisorProcess implements Stringable
{
    private string $value;

    public function __construct(string $value)
    {
        Assert::notEmpty($value);
        Assert::regex($value, '/^[a-z0-9_\-]+$/i', );

        $this->value = $value;
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
