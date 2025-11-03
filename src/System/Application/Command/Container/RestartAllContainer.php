<?php

namespace Marvin\System\Application\Command\Container;

use EnderLab\DddCqrsBundle\Application\Command\CommandInterface;
use EnderLab\MarvinManagerBundle\Reference\ManagerContainerActionReference;
use Marvin\Shared\Domain\ValueObject\Identity\CorrelationId;
use Marvin\System\Domain\ValueObject\Identity\ContainerId;

final readonly class RestartAllContainer implements CommandInterface
{
    public ManagerContainerActionReference $action;

    public function __construct(
        public ContainerId $containerId,
        public CorrelationId $correlationId,
        public int $timeout = 10,
    ) {
        $this->action = ManagerContainerActionReference::ACTION_RESTART_ALL;
    }
}
