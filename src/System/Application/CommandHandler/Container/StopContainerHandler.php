<?php

namespace Marvin\System\Application\CommandHandler\Container;

use EnderLab\MarvinManagerBundle\Messenger\Bus\MarvinToManagerCommandBusInterface;
use EnderLab\MarvinManagerBundle\Messenger\Request\ManagerRequestCommand;
use EnderLab\MarvinManagerBundle\Reference\ManagerContainerActionReference;
use Marvin\System\Application\Command\Container\StopContainer;
use Marvin\System\Domain\Exception\ActionNotAllowed;
use Marvin\System\Domain\Model\ActionRequest;
use Marvin\System\Domain\Repository\ActionRequestRepositoryInterface;
use Marvin\System\Domain\Repository\ContainerRepositoryInterface;
use Marvin\System\Domain\ValueObject\ActionStatus;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class StopContainerHandler
{
    public function __construct(
        private ContainerRepositoryInterface $containerRepository,
        private ActionRequestRepositoryInterface $actionRequestRepository,
        private MarvinToManagerCommandBusInterface $marvinToManagerCommandBus,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(StopContainer $command): void
    {
        $this->logger->info('Dispatching stop container command', [
            'correlationId' => $command->correlationId->toString(),
            'containerId' => $command->containerId->toString(),
        ]);

        $container = $this->containerRepository->byId($command->containerId);

        if (!$container->isActionAllowed(ManagerContainerActionReference::ACTION_STOP->value)) {
            throw ActionNotAllowed::withContainerAndAction(
                $container->serviceLabel,
                ManagerContainerActionReference::ACTION_STOP->value
            );
        }

        $actionRequest = new ActionRequest(
            $command->correlationId,
            'container',
            $command->containerId->toString(),
            ManagerContainerActionReference::ACTION_STOP->value,
            ActionStatus::PENDING,
        );

        $this->actionRequestRepository->save($actionRequest);

        $managerMessage = new ManagerRequestCommand(
            $command->containerId->toString(),
            $command->correlationId->toString(),
            ManagerContainerActionReference::ACTION_STOP->value,
            $command->timeout
        );

        $this->marvinToManagerCommandBus->dispatch($managerMessage);

        $this->logger->info('Stop container command dispatched', [
            'correlationId' => $command->correlationId,
        ]);
    }
}
