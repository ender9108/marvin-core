<?php

namespace Marvin\Device\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\ValueObject\ValueObjectTrait;
use Stringable;

final readonly class State implements Stringable
{
    use ValueObjectTrait;

    private function __construct(
        public array $value
    ) {
    }

    public static function fromArray(array $values): self
    {
        return new self($values);
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
        $values = $this->value;
        $values[$key] = $value;
        return new self($values);
    }

    public function without(string $key): self
    {
        $values = $this->value;
        unset($values[$key]);
        return new self($values);
    }

    public function merge(array $additionalValues): self
    {
        return new self(array_merge($this->value, $additionalValues));
    }

    public function __toString(): string
    {
        return json_encode($this->value);
    }
}

