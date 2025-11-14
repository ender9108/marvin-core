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

namespace Marvin\Device\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\ValueObject\ValueObjectTrait;
use Stringable;

final readonly class SceneStates implements Stringable
{
    use ValueObjectTrait;

    /**
     * @param array<string, array<string, array<string, mixed>>> $states
     *        Format: ['deviceId' => ['capability' => ['state' => value]]]
     */
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

    public function getDeviceState(string $deviceId): ?array
    {
        return $this->value[$deviceId] ?? null;
    }

    public function hasDevice(string $deviceId): bool
    {
        return isset($this->value[$deviceId]);
    }

    public function withDeviceState(string $deviceId, array $state): self
    {
        $states = $this->value;
        $states[$deviceId] = $state;
        return new self($states);
    }

    public function withoutDevice(string $deviceId): self
    {
        $states = $this->value;
        unset($states[$deviceId]);
        return new self($states);
    }

    public function getDeviceIds(): array
    {
        return array_keys($this->value);
    }

    public function __toString(): string
    {
        return (string) json_encode($this->value);
    }
}
