<?php

namespace Marvin\Device\Presentation\Cli\Command;

use EnderLab\DddCqrsBundle\Application\Query\QueryBusInterface;
use Marvin\Device\Application\Query\Group\GetGroupsCollection;
use Marvin\Device\Domain\Model\Device;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'marvin:device:list-groups',
    description: 'List all device groups'
)]
final readonly class ListGroupsCommand
{
    public function __construct(
        private QueryBusInterface $queryBus,
    ) {
    }

    public function __invoke(SymfonyStyle $io): int
    {
        $groups = $this->queryBus->handle(new GetGroupsCollection());

        if (empty($groups)) {
            $io->info('No groups found');

            return Command::SUCCESS;
        }

        $io->title('Device Groups');

        $rows = [];
        /** @var Device $group */
        foreach ($groups as $group) {
            $nativeInfo = $group->nativeGroupInfo;

            $rows[] = [
                substr($group->id->toString(), 0, 8),
                $group->label->value,
                count($group->childDeviceIds),
                $group->compositeStrategy->value,
                $nativeInfo && $nativeInfo->isSupported
                    ? "✓ {$nativeInfo->protocolType}"
                    : '✗ Emulated',
                $group->status->value,
            ];
        }

        $io->table(
            ['ID', 'Label', 'Devices', 'Strategy', 'Native', 'Status'],
            $rows
        );

        $io->note(sprintf('Total: %d groups', count($groups)));

        return Command::SUCCESS;
    }
}
