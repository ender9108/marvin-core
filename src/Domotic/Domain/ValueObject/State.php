<?php

namespace Marvin\Domotic\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use EnderLab\DddCqrsBundle\Domain\ValueObject\ValueObjectInterface;
use Marvin\Shared\Domain\ValueObject\ArrayValueObjectInterface;
use Stringable;

final readonly class State implements ArrayValueObjectInterface
{
    public array $value;

    public function __construct(array $state)
    {
        $this->value = $state;
    }

    public function equals(State $state): bool
    {
        return $this->value === $state->value;
    }

    public function toArray(): array
    {
        return $this->value;
    }
}
