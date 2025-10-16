<?php

namespace EnderLab\DddCqrsBundle\System\Application\CommandHandler\Container;

use EnderLab\DddCqrsBundle\Application\Command\CommandBusInterface;
use EnderLab\DddCqrsBundle\Application\Command\SyncCommandHandlerInterface;
use EnderLab\MarvinManagerBundle\Messenger\ManagerRequestCommand;
use EnderLab\MarvinManagerBundle\Reference\ManagerActionReference;
use Marvin\System\Application\Command\Container\StartContainer;
use Marvin\System\Domain\Exception\ActionNotAllowed;
use Marvin\System\Domain\Model\ActionRequest;
use Marvin\System\Domain\Repository\ActionRequestRepositoryInterface;
use Marvin\System\Domain\Repository\ContainerRepositoryInterface;
use Marvin\System\Domain\ValueObject\ActionStatus;
use Psr\Log\LoggerInterface;

final readonly class StartContainerHandler implements SyncCommandHandlerInterface
{
    public function __construct(
        private ContainerRepositoryInterface $containerRepository,
        private ActionRequestRepositoryInterface $actionRequestRepository,
        private CommandBusInterface $commandBus,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(StartContainer $command): void
    {
        $this->logger->info('Dispatching start container command', [
            'correlationId' => $command->correlationId->toString(),
            'containerId' => $command->containerId->toString(),
        ]);

        $container = $this->containerRepository->byId($command->containerId);

        if (!$container->isActionAllowed(ManagerActionReference::ACTION_START->value)) {
            throw ActionNotAllowed::withContainerAndAction(
                $container->label,
                ManagerActionReference::ACTION_START->value
            );
        }

        $actionRequest = new ActionRequest(
            $command->correlationId,
            'container',
            $command->containerId->toString(),
            ManagerActionReference::ACTION_START->value,
            ActionStatus::PENDING,
        );

        $this->actionRequestRepository->save($actionRequest);

        $managerMessage = new ManagerRequestCommand(
            $command->containerId->toString(),
            $command->correlationId->toString(),
            ManagerActionReference::ACTION_START->value,
            $command->timeout,
        );

        $this->commandBus->dispatch($managerMessage);

        $this->logger->info('Start container command dispatched', [
            'correlationId' => $command->correlationId,
        ]);
    }
}
