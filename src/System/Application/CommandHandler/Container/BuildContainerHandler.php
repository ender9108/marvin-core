<?php

namespace Marvin\System\Application\CommandHandler\Container;

use EnderLab\DddCqrsBundle\Application\Command\CommandBusInterface;
use EnderLab\DddCqrsBundle\Application\Command\SyncCommandHandlerInterface;
use EnderLab\MarvinManagerBundle\Messenger\ManagerRequestCommand;
use EnderLab\MarvinManagerBundle\Reference\ManagerActionReference;
use Marvin\System\Application\Command\Container\BuildContainer;
use Marvin\System\Domain\Exception\ActionNotAllowed;
use Marvin\System\Domain\Model\ActionRequest;
use Marvin\System\Domain\Repository\ActionRequestRepositoryInterface;
use Marvin\System\Domain\Repository\ContainerRepositoryInterface;
use Marvin\System\Domain\ValueObject\ActionStatus;
use Psr\Log\LoggerInterface;

final readonly class BuildContainerHandler implements SyncCommandHandlerInterface
{
    public function __construct(
        private ContainerRepositoryInterface $containerRepository,
        private ActionRequestRepositoryInterface $actionRequestRepository,
        private CommandBusInterface $commandBus,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(BuildContainer $command): void
    {
        $this->logger->info('Dispatching build container command', [
            'correlationId' => $command->correlationId->toString(),
            'containerId' => $command->containerId->toString(),
        ]);

        $container = $this->containerRepository->byId($command->containerId);

        if (!$container->isActionAllowed(ManagerActionReference::ACTION_BUILD->value)) {
            throw ActionNotAllowed::withContainerAndAction(
                $container->label,
                ManagerActionReference::ACTION_BUILD->value
            );
        }

        $actionRequest = new ActionRequest(
            $command->correlationId,
            'container',
            $command->containerId->toString(),
            ManagerActionReference::ACTION_BUILD->value,
            ActionStatus::PENDING,
        );

        $this->actionRequestRepository->save($actionRequest);

        $managerMessage = new ManagerRequestCommand(
            $command->containerId->toString(),
            $command->correlationId->toString(),
            ManagerActionReference::ACTION_BUILD->value
        );

        $this->commandBus->dispatch($managerMessage);

        $this->logger->info('Build container command dispatched', [
            'correlationId' => $command->correlationId,
        ]);
    }
}
