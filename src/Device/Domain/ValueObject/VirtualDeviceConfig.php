<?php

namespace Marvin\Device\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use EnderLab\DddCqrsBundle\Domain\ValueObject\ValueObjectTrait;
use Stringable;

final readonly class VirtualDeviceConfig implements Stringable
{
    use ValueObjectTrait;

    private array $value;

    private function __construct(array $value)
    {
        $this->value = $value;
    }

    public static function fromArray(array $value): self
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

    public function __toString(): string
    {
        return json_encode($this->value);
    }
}
