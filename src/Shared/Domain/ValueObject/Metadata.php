<?php

namespace Marvin\Shared\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\ValueObject\ValueObjectTrait;

final readonly class Metadata
{
    use ValueObjectTrait;

    public array $value;

    public function __construct(
        array $value,
    ) {
        $this->value = $value;
    }

    public static function fromArray(array $value): Metadata
    {
        return new self($value);
    }

    public static function empty(): self
    {
        return new self([]);
    }

    public function toArray(): array
    {
        return $this->value;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->value[$key] ?? $default;
    }

    public function has(string $key): bool
    {
        return isset($this->value[$key]);
    }

    public function with(string $key, mixed $value): self
    {
        $data = $this->value;
        $data[$key] = $value;
        return new self($data);
    }

    public function toString(): string
    {
        return json_encode($this->value);
    }
}
