<?php

declare(strict_types=1);

namespace Marvin\Device\Domain\Event\Device;

use EnderLab\DddCqrsBundle\Domain\Event\AbstractDomainEvent;

/**
 * Domain Event: Device state changed
 *
 * Emitted when a device's capability values are updated
 * Consumed by Automation and Telemetry contexts
 */
final readonly class DeviceStateChanged extends AbstractDomainEvent
{
    /**
     * @param array<string, mixed> $oldState Previous state values
     * @param array<string, mixed> $newState New state values
     */
    public function __construct(
        public string $deviceId,
        public array $oldState,
        public array $newState,
    ) {
        parent::__construct();
    }

    public function toArray(): array
    {
        return [
            'device_id' => $this->deviceId,
            'old_state' => $this->oldState,
            'new_state' => $this->newState,
        ];
    }

    /**
     * Get list of capability keys that changed
     *
     * @return string[]
     */
    public function getChangedCapabilities(): array
    {
        $changed = [];

        foreach ($this->newState as $key => $value) {
            if (!isset($this->oldState[$key]) || $this->oldState[$key] !== $value) {
                $changed[] = $key;
            }
        }

        return $changed;
    }
}
