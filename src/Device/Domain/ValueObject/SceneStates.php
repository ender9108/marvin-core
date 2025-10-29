<?php

namespace Marvin\Device\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use Marvin\Shared\Domain\ValueObject\ArrayValueObjectInterface;
use Marvin\Shared\Domain\ValueObject\Identity\DeviceId;

final readonly class SceneStates
{
    /**
     * Format: [
     *   'device-id-1' => ['light' => ['brightness' => 80, 'color_temp' => 'warm']],
     *   'device-id-2' => ['light' => ['state' => 'OFF']],
     * ]
     */
    private array $states;

    private function __construct(array $states)
    {
        Assert::notEmpty($states);

        foreach ($states as $deviceId => $capabilityStates) {
            Assert::uuid($deviceId);
            Assert::isArray($capabilityStates);
            Assert::notEmpty($capabilityStates);
        }

        $this->states = $states;
    }

    public static function fromArray(array $states): self
    {
        return new self($states);
    }

    public function getStateForDevice(DeviceId $deviceId): ?array
    {
        return $this->states[$deviceId->toString()] ?? null;
    }

    public function hasDevice(DeviceId $deviceId): bool
    {
        return isset($this->states[$deviceId->toString()]);
    }

    public function getDeviceIds(): array
    {
        return array_keys($this->states);
    }

    public function addState(DeviceId $deviceId, array $state): SceneStates
    {
        return new self([...$this->states, $deviceId->toString() => $state]);
    }

    public function removeState(DeviceId $deviceId): SceneStates
    {
        $states = $this->states;

        if (isset($states[$deviceId->toString()])) {
            unset($states[$deviceId->toString()]);
        }

        return new self($states);
    }

    public function toArray(): array
    {
        return $this->states;
    }

    public function equals(self $other): bool
    {
        return $this->toArray() === $other->toArray();
    }
}
