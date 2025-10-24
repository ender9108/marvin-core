<?php

namespace Marvin\System\Application\Command\Container;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandInterface;
use EnderLab\MarvinManagerBundle\Reference\ManagerContainerActionReference;
use Marvin\Shared\Domain\ValueObject\Identity\UniqId;
use Marvin\System\Domain\ValueObject\Identity\ContainerId;

final readonly class RestartAllContainer implements SyncCommandInterface
{
    public ManagerContainerActionReference $action;

    public function __construct(
        public ContainerId $containerId,
        public UniqId $correlationId,
        public int $timeout = 10,
    ) {
        $this->action = ManagerContainerActionReference::ACTION_RESTART_ALL;
    }
}
