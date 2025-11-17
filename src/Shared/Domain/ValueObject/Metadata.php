<?php

/**
 * Marvin Core - DDD-based home automation system
 *
 * @package   Marvin\Core
 * @author    Alexandre Berthelot <alexandreberthelot9108@gmail.com>
 * @copyright 2024-present Alexandre Berthelot
 * @license   AGPL-3.0 License
 * @link      https://github.com/ender9108/marvin-core
 */

declare(strict_types=1);

namespace Marvin\Shared\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\ValueObject\ValueObjectTrait;

final readonly class Metadata
{
    use ValueObjectTrait;

    public function __construct(
        public ?array $value,
    ) {
    }

    public static function fromArray(?array $value): Metadata
    {
        return new self($value);
    }

    public static function empty(): self
    {
        return new self([]);
    }

    public function toArray(): array
    {
        if (null === $this->value) {
            return [];
        }

        return $this->value;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this?->value[$key] ?? $default;
    }

    public function has(string $key): bool
    {
        return isset($this?->value[$key]);
    }

    public function with(string $key, mixed $value): self
    {
        $data = $this->value ?? [];
        $data[$key] = $value;
        return new self($data);
    }

    public function toString(): string
    {
        return json_encode($this->value ?? []);
    }
}
