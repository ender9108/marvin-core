<?php

namespace Marvin\Shared\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\ValueObject\ValueObjectInterface;

final readonly class Metadata implements ArrayValueObjectInterface
{
    public function __construct(public array $value = [])
    {
    }

    public static function fromArray(array $value): Metadata
    {
        return new self($value);
    }

    public function equals(ValueObjectInterface $other): bool
    {
        return $this->value === $other->value;
    }

    public function toArray(): array
    {
        return $this->value;
    }
}
