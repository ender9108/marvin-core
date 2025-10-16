<?php

namespace Marvin\System\Presentation\Cli;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandBusInterface;
use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use EnderLab\MarvinManagerBundle\Reference\ManagerActionReference;
use Marvin\Shared\Domain\ValueObject\Identity\UniqId;
use Marvin\Shared\Presentation\Exception\Service\ExceptionMessageManager;
use Marvin\System\Application\Command\Container\BuildContainer;
use Marvin\System\Application\Command\Container\ExecContainerCommand;
use Marvin\System\Application\Command\Container\RestartContainer;
use Marvin\System\Application\Command\Container\StartContainer;
use Marvin\System\Application\Command\Container\StopContainer;
use Marvin\System\Domain\Exception\ActionNotAllowed;
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
        private SyncCommandBusInterface $syncCommandBus,
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
            Assert::inArray($action, ManagerActionReference::values());

            $containerId = new ContainerId($id);
            $correlationId = new UniqId();
            $command = match ($action) {
                ManagerActionReference::ACTION_START->value => new StartContainer($containerId, $correlationId, $timeout),
                ManagerActionReference::ACTION_STOP->value => new StopContainer($containerId, $correlationId, $timeout),
                ManagerActionReference::ACTION_RESTART->value => new RestartContainer($containerId, $correlationId, $timeout),
                ManagerActionReference::ACTION_BUILD->value => new BuildContainer($containerId, $correlationId, $timeout),
                ManagerActionReference::ACTION_EXEC_CMD->value => new ExecContainerCommand(
                    $containerId,
                    $correlationId,
                    $timeout,
                    $dockerCommand,
                    $args
                ),
                default => throw ActionNotAllowed::withContainerAndAction($containerId, $action),
            };

            $this->syncCommandBus->handle($command);

            $io->success(sprintf('Command %s sent to container %s successfully.', $action, $id));

            return Command::SUCCESS;
        } catch (DomainException $de) {
            $io->error($this->exceptionMessageManager->cliResponseFormat($de));

            return Command::FAILURE;
        }
    }
}
