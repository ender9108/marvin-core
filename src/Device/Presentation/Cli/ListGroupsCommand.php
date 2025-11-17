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
use Marvin\Device\Application\Query\Group\GetGroupsCollection;
use Marvin\Device\Domain\Model\Device;
use Marvin\Shared\Presentation\Exception\Service\ExceptionMessageManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'marvin:device:list-groups',
    description: 'List all device groups',
)]
final readonly class ListGroupsCommand
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
            /** @var array<Device> $groups */
            $groups = $this->queryBus->handle(new GetGroupsCollection());

            if (empty($groups)) {
                $io->info('No groups found.');
                return Command::SUCCESS;
            }

            $rows = [];
            foreach ($groups as $group) {
                $rows[] = [
                    $group->id->toString(),
                    $group->label->value,
                    $group->compositeStrategy?->value ?? 'N/A',
                    $group->executionStrategy?->value ?? 'N/A',
                    count($group->childDeviceIds),
                    $group->status->value,
                    $group->nativeGroupInfo !== null ? '✓' : '✗',
                ];
            }

            $io->table(
                ['ID', 'Label', 'Strategy', 'Execution', 'Devices', 'Status', 'Native'],
                $rows
            );

            $io->success(sprintf('Found %d group(s).', count($groups)));

            return Command::SUCCESS;
        } catch (Exception $e) {
            $io->error($this->exceptionMessageManager->cliResponseFormat($e));

            return Command::FAILURE;
        }
    }
}
