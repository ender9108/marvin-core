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

final readonly class ProtocolStatusChanged extends AbstractDomainEvent
{
    public function __construct(
        public string $protocolId,
        public string $previousStatus,
        public string $newStatus,
        public ?string $errorMessage = null,
    ) {
        parent::__construct();
    }

    public function toArray(): array
    {
        return [
            'protocol_id' => $this->protocolId,
            'previous_status' => $this->previousStatus,
            'new_status' => $this->newStatus,
            'error_message' => $this->errorMessage,
        ];
    }
}
