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
use Exception;
use Marvin\Shared\Domain\ValueObject\Identity\CorrelationId;
use Marvin\Shared\Presentation\Exception\Service\ExceptionMessageManager;
use Marvin\System\Application\Command\Container\BuildContainer;
use Marvin\System\Domain\ValueObject\Identity\ContainerId;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'marvin:system:build-container',
    description: 'Build a container',
)]
final readonly class BuildContainerCommand
{
    public function __construct(
        private CommandBusInterface $commandBus,
        private ExceptionMessageManager $exceptionMessageManager,
    ) {
    }

    public function __invoke(
        SymfonyStyle $io,
        #[Argument(description: 'Container ID', name: 'container-id')]
        string $containerId,
        #[Option(description: 'Timeout in seconds', name: 'timeout')]
        int $timeout = 10,
    ): int {
        try {
            $command = new BuildContainer(
                containerId: new ContainerId($containerId),
                correlationId: new CorrelationId(),
                timeout: $timeout,
            );

            $this->commandBus->dispatch($command);

            $io->success(sprintf('Container %s build request sent successfully.', $containerId));

            return Command::SUCCESS;
        } catch (Exception $e) {
            $io->error($this->exceptionMessageManager->cliResponseFormat($e));

            return Command::FAILURE;
        }
    }
}
