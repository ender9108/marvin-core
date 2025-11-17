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

namespace Marvin\Protocol\Domain\Event;

use EnderLab\DddCqrsBundle\Domain\Event\AbstractDomainEvent;

final readonly class ProtocolCommandSent extends AbstractDomainEvent
{
    public function __construct(
        public string $protocolId,
        public string $deviceId,
        public string $action,
        public array $parameters,
        public string $executionMode,
        public ?string $correlationId = null,
    ) {
        parent::__construct();
    }

    public function toArray(): array
    {
        return [
            'protocol_id' => $this->protocolId,
            'device_id' => $this->deviceId,
            'action' => $this->action,
            'parameters' => $this->parameters,
            'execution_mode' => $this->executionMode,
            'correlation_id' => $this->correlationId,
        ];
    }
}
