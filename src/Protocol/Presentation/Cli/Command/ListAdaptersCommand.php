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

namespace Marvin\Protocol\Presentation\Cli\Command;

use EnderLab\DddCqrsBundle\Application\Query\QueryBusInterface;
use Marvin\Protocol\Application\Query\ListAvailableAdapters;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'protocol:adapter:list',
    description: 'List all available protocol adapters',
)]
final readonly class ListAdaptersCommand
{
    public function __construct(
        private QueryBusInterface $queryBus,
    ) {
    }

    public function __invoke(
        SymfonyStyle $io,
        #[Option(description: 'Filter by protocol type (mqtt, rest, jsonrpc, websocket)', name: 'type')]
        ?string $type = null,
    ): int {
        $io->title('Protocol Adapters');

        $query = new ListAvailableAdapters($type);
        $adapters = $this->queryBus->handle($query);

        if (empty($adapters)) {
            $io->warning('No adapters found');
            return Command::SUCCESS;
        }

        $rows = [];
        foreach ($adapters as $adapter) {
            $rows[] = [
                $adapter->name,
                $adapter->protocolType,
                $adapter->defaultExecutionMode,
                implode(', ', $adapter->supportedExecutionModes),
                $adapter->description,
            ];
        }

        $io->table(
            ['Name', 'Protocol Type', 'Default Mode', 'Supported Modes', 'Description'],
            $rows
        );

        $io->success(sprintf('Found %d adapter(s)', count($adapters)));

        return Command::SUCCESS;
    }
}
