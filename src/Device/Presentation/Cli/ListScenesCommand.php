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

use EnderLab\DddCqrsBundle\Application\Query\QueryBusInterface;
use Exception;
use Marvin\Device\Application\Query\Scene\GetScenesCollection;
use Marvin\Device\Domain\Model\Device;
use Marvin\Shared\Presentation\Exception\Service\ExceptionMessageManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'marvin:device:list-scenes',
    description: 'List all scenes',
)]
final readonly class ListScenesCommand
{
    public function __construct(
        private QueryBusInterface $queryBus,
        private ExceptionMessageManager $exceptionMessageManager,
    ) {
    }

    public function __invoke(
        SymfonyStyle $io,
    ): int {
        try {
            /** @var array<Device> $scenes */
            $scenes = $this->queryBus->handle(new GetScenesCollection());

            if (empty($scenes)) {
                $io->info('No scenes found.');
                return Command::SUCCESS;
            }

            $rows = [];
            foreach ($scenes as $scene) {
                $hasStates = $scene->sceneStates !== null && !empty($scene->sceneStates->toArray());

                $rows[] = [
                    $scene->id->toString(),
                    $scene->label->value,
                    $scene->compositeStrategy?->value ?? 'N/A',
                    $scene->executionStrategy?->value ?? 'N/A',
                    $hasStates ? count($scene->sceneStates->toArray()) : 0,
                    $scene->status->value,
                    $scene->nativeSceneInfo !== null ? '✓' : '✗',
                ];
            }

            $io->table(
                ['ID', 'Label', 'Strategy', 'Execution', 'States', 'Status', 'Native'],
                $rows
            );

            $io->success(sprintf('Found %d scene(s).', count($scenes)));

            return Command::SUCCESS;
        } catch (Exception $e) {
            $io->error($this->exceptionMessageManager->cliResponseFormat($e));

            return Command::FAILURE;
        }
    }
}
