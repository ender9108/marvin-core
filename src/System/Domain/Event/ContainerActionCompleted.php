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

namespace Marvin\System\Domain\Event;

use EnderLab\DddCqrsBundle\Domain\Event\AbstractDomainEvent;
use Marvin\Shared\Domain\ValueObject\Identity\UniqId;
use Marvin\System\Domain\ValueObject\Identity\ContainerId;

final readonly class ContainerActionCompleted extends AbstractDomainEvent
{
    public function __construct(
        public UniqId $correlationId,
        public ContainerId $containerId,
        public string $action,
        public bool $success,
        public ?string $output = null,
        public ?string $error = null,
    ) {
        parent::__construct();
    }

    public function toArray(): array
    {
        return [
            'correlation_id' => $this->correlationId->toString(),
            'container_id' => $this->containerId->toString(),
            'action' => $this->action,
            'success' => $this->success,
            'output' => $this->output,
            'error' => $this->error,
        ];
    }
}
