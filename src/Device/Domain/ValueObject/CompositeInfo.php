<?php

namespace Marvin\Device\Domain\ValueObject;

use Stringable;

final readonly class CompositeInfo implements Stringable
{
    /**
     * @param string[] $childDeviceIds
     */
    private function __construct(
        private CompositeStrategy $strategy,
        private ExecutionStrategy $executionStrategy,
        private array $childDeviceIds,
        private ?string $nativeGroupId = null,
        private bool $isNativeSupported = false
    ) {
    }

    /**
     * @param string[] $childDeviceIds
     */
    public static function create(
        CompositeStrategy $strategy,
        ExecutionStrategy $executionStrategy,
        array $childDeviceIds,
        ?string $nativeGroupId = null,
        bool $isNativeSupported = false
    ): self {
        return new self($strategy, $executionStrategy, $childDeviceIds, $nativeGroupId, $isNativeSupported);
    }

    public function getStrategy(): CompositeStrategy
    {
        return $this->strategy;
    }

    public function getExecutionStrategy(): ExecutionStrategy
    {
        return $this->executionStrategy;
    }

    public function getChildDeviceIds(): array
    {
        return $this->childDeviceIds;
    }

    public function getNativeGroupId(): ?string
    {
        return $this->nativeGroupId;
    }

    public function isNativeSupported(): bool
    {
        return $this->isNativeSupported;
    }

    public function withNativeGroupId(string $nativeGroupId): self
    {
        return new self(
            $this->strategy,
            $this->executionStrategy,
            $this->childDeviceIds,
            $nativeGroupId,
            true
        );
    }

    public function addChildDevice(string $deviceId): self
    {
        if (in_array($deviceId, $this->childDeviceIds, true)) {
            return $this;
        }

        $childDeviceIds = $this->childDeviceIds;
        $childDeviceIds[] = $deviceId;

        return new self(
            $this->strategy,
            $this->executionStrategy,
            $childDeviceIds,
            $this->nativeGroupId,
            $this->isNativeSupported
        );
    }

    public function removeChildDevice(string $deviceId): self
    {
        $childDeviceIds = array_filter(
            $this->childDeviceIds,
            fn(string $id) => $id !== $deviceId
        );

        return new self(
            $this->strategy,
            $this->executionStrategy,
            array_values($childDeviceIds),
            $this->nativeGroupId,
            $this->isNativeSupported
        );
    }

    public function toArray(): array
    {
        return [
            'strategy' => $this->strategy->value,
            'execution_strategy' => $this->executionStrategy->value,
            'child_device_ids' => $this->childDeviceIds,
            'native_group_id' => $this->nativeGroupId,
            'is_native_supported' => $this->isNativeSupported,
        ];
    }

    public function equals(self $other): bool
    {
        return $this->strategy === $other->strategy
            && $this->executionStrategy === $other->executionStrategy
            && $this->childDeviceIds === $other->childDeviceIds
            && $this->nativeGroupId === $other->nativeGroupId
            && $this->isNativeSupported === $other->isNativeSupported;
    }

    public function __toString(): string
    {
        return json_encode($this->toArray());
    }
}

