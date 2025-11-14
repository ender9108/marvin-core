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

namespace Marvin\Device\Domain\Event\Device;

use EnderLab\DddCqrsBundle\Domain\Event\AbstractDomainEvent;

/**
 * Domain Event: A device was created
 *
 * Emitted when a new device (physical, virtual, or composite) is created
 */
final readonly class DeviceCreated extends AbstractDomainEvent
{
    public function __construct(
        public string $deviceId,
        public string $label,
        public string $deviceType,
        public ?string $protocol = null,
    ) {
        parent::__construct();
    }

    public function toArray(): array
    {
        return [
            'device_id' => $this->deviceId,
            'label' => $this->label,
            'device_type' => $this->deviceType,
            'protocol' => $this->protocol,
        ];
    }
}
