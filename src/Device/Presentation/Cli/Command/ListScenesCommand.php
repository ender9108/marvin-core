<?php

namespace Marvin\Device\Presentation\Cli\Command;

use EnderLab\DddCqrsBundle\Application\Query\QueryBusInterface;
use Marvin\Device\Application\Query\Scene\GetScenesCollection;
use Marvin\Device\Domain\Model\Device;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'marvin:device:list-scenes',
    description: 'List all scenes'
)]
final readonly class ListScenesCommand
{
    public function __construct(
        private QueryBusInterface $queryBus
    ) {
    }

    protected function execute(SymfonyStyle $io): int
    {
        $scenes = $this->queryBus->handle(new GetScenesCollection());

        if (empty($scenes)) {
            $io->info('No scenes found');

            return Command::SUCCESS;
        }

        $io->title('Scenes');

        $rows = [];
        /** @var Device $scene */
        foreach ($scenes as $scene) {
            $nativeInfo = $scene->nativeSceneInfo;

            $rows[] = [
                substr($scene->id->toString(), 0, 8),
                $scene->label->value,
                count($scene->childDeviceIds),
                $nativeInfo && $nativeInfo->isSupported
                    ? "✓ {$nativeInfo->protocolType}"
                    : '✗ Emulated',
            ];
        }

        $io->table(
            ['ID', 'Label', 'Devices', 'Native'],
            $rows
        );

        $io->note(sprintf('Total: %d scenes', count($scenes)));

        return Command::SUCCESS;
    }
}
