<?php

namespace Marvin\Shared\Domain\ValueObject;

final readonly class Metadata
{
    public function __construct(public array $value = [])
    {
    }

    public static function fromArray(array $value): Metadata
    {
        return new self($value);
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function toArray(): array
    {
        return $this->value;
    }
}
