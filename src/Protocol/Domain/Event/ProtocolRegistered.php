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

final readonly class ProtocolRegistered extends AbstractDomainEvent
{
    public function __construct(
        public string $protocolId,
        public string $name,
        public string $transportType,
        public array $configuration,
        public string $preferredExecutionMode,
    ) {
        parent::__construct();
    }

    public function toArray(): array
    {
        return [
            'protocol_id' => $this->protocolId,
            'name' => $this->name,
            'transport_type' => $this->transportType,
            'configuration' => $this->configuration,
            'preferred_execution_mode' => $this->preferredExecutionMode,
        ];
    }
}
