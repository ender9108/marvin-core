<?php

namespace Marvin\System\Application\CommandHandler\Worker;

use EnderLab\DddCqrsBundle\Application\Command\CommandBusInterface;
use EnderLab\DddCqrsBundle\Application\Command\SyncCommandHandlerInterface;
use Marvin\System\Application\Command\Container\StopContainer;
use Marvin\System\Domain\Repository\ActionRequestRepositoryInterface;
use Marvin\System\Domain\Repository\ContainerRepositoryInterface;
use Marvin\System\Domain\Repository\WorkerRepositoryInterface;
use Psr\Log\LoggerInterface;

final readonly class StartWorkerHandler implements SyncCommandHandlerInterface
{
    public function __construct(
        private ContainerRepositoryInterface $containerRepository,
        private WorkerRepositoryInterface $workerRepository,
        private ActionRequestRepositoryInterface $actionRequestRepository,
        private CommandBusInterface $commandBus,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(): void
    {

    }
}
