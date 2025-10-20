<?php

namespace Marvin\Shared\Domain\ValueObject;

final readonly class Metadata implements ArrayValueObjectInterface
{
    public function __construct(public array $value = []) {
    }

    public function equals(Metadata $metadata): bool
    {
        return $this->value === $metadata->value;
    }

    public function toArray(): array
    {
        return $this->value;
    }
}
