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

namespace Marvin\System\Presentation\Cli;

use EnderLab\DddCqrsBundle\Application\Command\CommandBusInterface;
use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use EnderLab\MarvinManagerBundle\Reference\ManagerContainerActionReference;
use Exception;
use Marvin\Shared\Domain\ValueObject\Identity\CorrelationId;
use Marvin\Shared\Presentation\Exception\Service\ExceptionMessageManager;
use Marvin\System\Application\Command\Container\BuildContainer;
use Marvin\System\Application\Command\Container\ExecContainerCommand;
use Marvin\System\Application\Command\Container\RestartAllContainer;
use Marvin\System\Application\Command\Container\RestartContainer;
use Marvin\System\Application\Command\Container\StartContainer;
use Marvin\System\Application\Command\Container\StopContainer;
use Marvin\System\Domain\Exception\ActionNotAllowed;
use Marvin\System\Domain\Repository\ContainerRepositoryInterface;
use Marvin\System\Domain\ValueObject\Identity\ContainerId;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'marvin:system:send-container-command',
    description: 'Send command to container (start, stop, restart, build, exec).',
)]
final readonly class SendContainerCommand
{
    public function __construct(
        private CommandBusInterface $commandBus,
        private ContainerRepositoryInterface $containerRepository,
        private ExceptionMessageManager $exceptionMessageManager,
    ) {
    }

    public function __invoke(
        SymfonyStyle $io,
        #[Argument(name: 'id')]
        string $id,
        #[Argument(name: 'action')]
        string $action,
        #[Option(name: 'command')]
        ?string $dockerCommand = null,
        #[Option(name: 'args')]
        array $args = [],
        #[Option(name: 'timeout')]
        int $timeout = 10,
    ): int {
        try {
            Assert::inArray($action, ManagerContainerActionReference::values(), 'system.exceptions.SY0011.invalid_action');
            ;

            $container = $this->containerRepository->byId(ContainerId::fromString($id));

            $correlationId = new CorrelationId();
            $command = match ($action) {
                ManagerContainerActionReference::ACTION_START->value => new StartContainer($container->id, $correlationId, $timeout),
                ManagerContainerActionReference::ACTION_STOP->value => new StopContainer($container->id, $correlationId, $timeout),
                ManagerContainerActionReference::ACTION_RESTART->value => new RestartContainer($container->id, $correlationId, $timeout),
                ManagerContainerActionReference::ACTION_RESTART_ALL->value => new RestartAllContainer($container->id, $correlationId, $timeout),
                ManagerContainerActionReference::ACTION_BUILD->value => new BuildContainer($container->id, $correlationId, $timeout),
                ManagerContainerActionReference::ACTION_EXEC_CMD->value => new ExecContainerCommand(
                    $container->id,
                    $correlationId,
                    $timeout,
                    $dockerCommand,
                    $args
                ),
                default => throw ActionNotAllowed::withContainerAndAction($container->id, $action),
            };

            $this->commandBus->dispatch($command);

            $io->success(sprintf('Command %s sent to container %s successfully.', $action, $id));

            return Command::SUCCESS;
        } catch (Exception $e) {
            $io->error($this->exceptionMessageManager->cliResponseFormat($e));

            return Command::FAILURE;
        }
    }
}
