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

namespace Marvin\System\Application\CommandHandler\Container;

use EnderLab\MarvinManagerBundle\Messenger\Bus\MarvinToManagerCommandBusInterface;
use EnderLab\MarvinManagerBundle\Messenger\Request\ManagerRequestCommand;
use EnderLab\MarvinManagerBundle\Reference\ManagerContainerActionReference;
use Marvin\System\Application\Command\Container\ExecContainerCommand;
use Marvin\System\Domain\Exception\ActionNotAllowed;
use Marvin\System\Domain\Model\ActionRequest;
use Marvin\System\Domain\Repository\ActionRequestRepositoryInterface;
use Marvin\System\Domain\Repository\ContainerRepositoryInterface;
use Marvin\System\Domain\ValueObject\ActionStatus;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class ExecContainerCommandHandler
{
    public function __construct(
        private ContainerRepositoryInterface $containerRepository,
        private ActionRequestRepositoryInterface $actionRequestRepository,
        private MarvinToManagerCommandBusInterface $marvinToManagerCommandBus,
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
        if (!$container->isActionAllowed(ManagerContainerActionReference::ACTION_EXEC_CMD->value)) {
            throw ActionNotAllowed::withContainerAndAction(
                $container->serviceLabel,
                ManagerContainerActionReference::ACTION_EXEC_CMD->value
            );
        }

        // Créer une ActionRequest pour tracker
        $actionRequest = new ActionRequest(
            $command->correlationId,
            'container',
            $command->containerId->toString(),
            ManagerContainerActionReference::ACTION_EXEC_CMD->value,
            ActionStatus::PENDING,
        );

        $this->actionRequestRepository->save($actionRequest);

        $managerMessage = new ManagerRequestCommand(
            $command->containerId->toString(),
            $command->correlationId->toString(),
            ManagerContainerActionReference::ACTION_EXEC_CMD->value,
            $command->command,
            $command->args
        );

        $this->marvinToManagerCommandBus->dispatch($managerMessage);

        $this->logger->info('Exec command in container dispatched', [
            'correlationId' => $command->correlationId,
        ]);
    }
}
