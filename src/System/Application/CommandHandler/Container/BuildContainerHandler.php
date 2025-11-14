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
use Marvin\System\Application\Command\Container\BuildContainer;
use Marvin\System\Domain\Exception\ActionNotAllowed;
use Marvin\System\Domain\Model\ActionRequest;
use Marvin\System\Domain\Repository\ActionRequestRepositoryInterface;
use Marvin\System\Domain\Repository\ContainerRepositoryInterface;
use Marvin\System\Domain\ValueObject\ActionStatus;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class BuildContainerHandler
{
    public function __construct(
        private ContainerRepositoryInterface $containerRepository,
        private ActionRequestRepositoryInterface $actionRequestRepository,
        private MarvinToManagerCommandBusInterface $marvinToManagerCommandBus,
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

        if (!$container->isActionAllowed(ManagerContainerActionReference::ACTION_BUILD->value)) {
            throw ActionNotAllowed::withContainerAndAction(
                $container->serviceLabel,
                ManagerContainerActionReference::ACTION_BUILD->value
            );
        }

        $actionRequest = new ActionRequest(
            $command->correlationId,
            'container',
            $command->containerId->toString(),
            ManagerContainerActionReference::ACTION_BUILD->value,
            ActionStatus::PENDING,
        );

        $this->actionRequestRepository->save($actionRequest);

        $managerMessage = new ManagerRequestCommand(
            $command->containerId->toString(),
            $command->correlationId->toString(),
            ManagerContainerActionReference::ACTION_BUILD->value
        );

        $this->marvinToManagerCommandBus->dispatch($managerMessage);

        $this->logger->info('Build container command dispatched', [
            'correlationId' => $command->correlationId,
        ]);
    }
}
