<?php

namespace Marvin\System\Presentation\Cli;

use EnderLab\DddCqrsBundle\Application\Command\CommandBusInterface;
use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use EnderLab\MarvinManagerBundle\Reference\ManagerContainerActionReference;
use Exception;
use Marvin\Shared\Domain\ValueObject\Identity\UniqId;
use Marvin\Shared\Presentation\Exception\Service\ExceptionMessageManager;
use Marvin\System\Application\Command\Container\BuildContainer;
use Marvin\System\Application\Command\Container\ExecContainerCommand;
use Marvin\System\Application\Command\Container\RestartAllContainer;
use Marvin\System\Application\Command\Container\RestartContainer;
use Marvin\System\Application\Command\Container\StartContainer;
use Marvin\System\Application\Command\Container\StopContainer;
use Marvin\System\Domain\Exception\ActionNotAllowed;
use Marvin\System\Domain\ValueObject\Identity\ContainerId;
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
        private ExceptionMessageManager $exceptionMessageManager,
    ) {
    }

    public function __invoke(
        SymfonyStyle $io,
        #[Option(name: 'id')]
        string $id,
        #[Option(name: 'action')]
        string $action,
        #[Option(name: 'command')]
        ?string $dockerCommand = null,
        #[Option(name: 'args')]
        array $args = [],
        #[Option(name: 'timeout')]
        int $timeout = 10,
    ): int {
        try {
            Assert::inArray($action, ManagerContainerActionReference::values());

            $containerId = new ContainerId($id);
            $correlationId = new UniqId();
            $command = match ($action) {
                ManagerContainerActionReference::ACTION_START->value => new StartContainer($containerId, $correlationId, $timeout),
                ManagerContainerActionReference::ACTION_STOP->value => new StopContainer($containerId, $correlationId, $timeout),
                ManagerContainerActionReference::ACTION_RESTART->value => new RestartContainer($containerId, $correlationId, $timeout),
                ManagerContainerActionReference::ACTION_RESTART_ALL->value => new RestartAllContainer($containerId, $correlationId, $timeout),
                ManagerContainerActionReference::ACTION_BUILD->value => new BuildContainer($containerId, $correlationId, $timeout),
                ManagerContainerActionReference::ACTION_EXEC_CMD->value => new ExecContainerCommand(
                    $containerId,
                    $correlationId,
                    $timeout,
                    $dockerCommand,
                    $args
                ),
                default => throw ActionNotAllowed::withContainerAndAction($containerId, $action),
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
