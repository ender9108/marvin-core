<?php

namespace Marvin\System\Domain\ValueObject;

use Marvin\Shared\Domain\ValueObject\ArrayValueObjectInterface;

final readonly class Metadata implements ArrayValueObjectInterface
{
    public array $value;

    public function __construct(array $metadata = [])
    {
        $this->value = $metadata;
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
