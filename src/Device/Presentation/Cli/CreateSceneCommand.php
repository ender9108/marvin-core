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

namespace Marvin\Device\Presentation\Cli;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandBusInterface;
use Exception;
use Marvin\Device\Application\Command\Scene\CreateScene;
use Marvin\Device\Domain\ValueObject\CompositeStrategy;
use Marvin\Device\Domain\ValueObject\ExecutionStrategy;
use Marvin\Shared\Domain\ValueObject\Label;
use Marvin\Shared\Presentation\Exception\Service\ExceptionMessageManager;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'marvin:device:create-scene',
    description: 'Create a scene',
)]
final readonly class CreateSceneCommand
{
    public function __construct(
        private SyncCommandBusInterface $syncCommandBus,
        private ExceptionMessageManager $exceptionMessageManager,
    ) {
    }

    public function __invoke(
        SymfonyStyle $io,
        #[Argument(description: 'Scene label', name: 'label')]
        string $label,
        #[Option(description: 'Composite strategy (native_if_available, native_only, emulated)', name: 'composite-strategy')]
        string $compositeStrategy = 'native_if_available',
        #[Option(description: 'Execution strategy (broadcast, sequential, optimized)', name: 'execution-strategy')]
        string $executionStrategy = 'sequential',
    ): int {
        try {
            $sceneId = $this->syncCommandBus->handle(new CreateScene(
                label: Label::fromString($label),
                compositeStrategy: CompositeStrategy::from($compositeStrategy),
                executionStrategy: ExecutionStrategy::from($executionStrategy),
            ));

            $io->success(sprintf('Scene "%s" created successfully with ID: %s', $label, $sceneId));
            $io->info('Use the store-scene-state command to capture device states for this scene.');

            return Command::SUCCESS;
        } catch (Exception $e) {
            $io->error($this->exceptionMessageManager->cliResponseFormat($e));

            return Command::FAILURE;
        }
    }
}
