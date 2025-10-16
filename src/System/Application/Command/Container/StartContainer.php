<?php

namespace Marvin\System\Application\Command\Container;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandInterface;
use Marvin\Shared\Domain\ValueObject\Identity\UniqId;
use Marvin\System\Domain\ValueObject\Identity\ContainerId;

final readonly class StartContainer implements SyncCommandInterface
{
    public function __construct(
        public ContainerId $containerId,
        public UniqId $correlationId,
        public int $timeout = 10,
    ) {
    }
}
