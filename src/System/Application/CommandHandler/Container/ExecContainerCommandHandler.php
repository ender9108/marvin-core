<?php

namespace EnderLab\DddCqrsBundle\System\Application\CommandHandler\Container;

use EnderLab\DddCqrsBundle\Application\Command\CommandBusInterface;
use EnderLab\DddCqrsBundle\Application\Command\SyncCommandHandlerInterface;
use EnderLab\MarvinManagerBundle\Messenger\ManagerRequestCommand;
use EnderLab\MarvinManagerBundle\Reference\ManagerActionReference;
use Marvin\System\Application\Command\Container\ExecContainerCommand;
use Marvin\System\Domain\Exception\ActionNotAllowed;
use Marvin\System\Domain\Model\ActionRequest;
use Marvin\System\Domain\Repository\ActionRequestRepositoryInterface;
use Marvin\System\Domain\Repository\ContainerRepositoryInterface;
use Marvin\System\Domain\ValueObject\ActionStatus;
use Psr\Log\LoggerInterface;

final readonly class ExecContainerCommandHandler implements SyncCommandHandlerInterface
{
    public function __construct(
        private ContainerRepositoryInterface $containerRepository,
        private ActionRequestRepositoryInterface $actionRequestRepository,
        private CommandBusInterface $commandBus,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(ExecContainerCommand $command): void
    {
        $this->logger->info('Dispatching exec command in container', [
            'correlationId' => $command->correlationId->toString(),
            'containerId' => $command->containerId->toString(),
        ]);

        $container = $this->containerRepository->byId($command->containerId);

        // Vérifier que l'action est autorisée
        if (!$container->isActionAllowed(ManagerActionReference::ACTION_EXECUTE_COMMAND_DOCKER->value)) {
            throw ActionNotAllowed::withContainerAndAction(
                $container->label,
                ManagerActionReference::ACTION_EXECUTE_COMMAND_DOCKER->value
            );
        }

        // Créer une ActionRequest pour tracker
        $actionRequest = new ActionRequest(
            $command->correlationId,
            'container',
            $command->containerId->toString(),
            ManagerActionReference::ACTION_EXECUTE_COMMAND_DOCKER->value,
            ActionStatus::PENDING,
        );

        $this->actionRequestRepository->save($actionRequest);

        $managerMessage = new ManagerRequestCommand(
            $command->containerId->toString(),
            $command->correlationId->toString(),
            ManagerActionReference::ACTION_EXECUTE_COMMAND_DOCKER->value,
            $command->command,
            $command->args
        );

        $this->commandBus->dispatch($managerMessage);

        $this->logger->info('Exec command in container dispatched', [
            'correlationId' => $command->correlationId,
        ]);
    }
}
