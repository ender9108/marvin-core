<?php

namespace Marvin\Location\Presentation\Cli;

use EnderLab\DddCqrsBundle\Application\Query\QueryBusInterface;
use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use Marvin\Location\Application\Query\Zone\GetZoneHierarchy;
use Marvin\Shared\Domain\ValueObject\Identity\ZoneId;
use Marvin\Shared\Presentation\Exception\Service\ExceptionMessageManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'marvin:location:show-hierarchy',
    description: 'Show zone hierarchy',
)]
final readonly class ShowZoneHierarchyCommand
{
    public function __construct(
        private QueryBusInterface $queryBus,
        private ExceptionMessageManager $exceptionMessageManager,
    ) {
    }

    public function __invoke(
        SymfonyStyle $io,
        #[Option(name: 'root-zone-id')]
        ?string $rootZoneId = null,
    ): int {
        try {
            $query = new GetZoneHierarchy(
                rootZoneId: null !== $rootZoneId ? ZoneId::fromString($rootZoneId) : null
            );
            $hierarchy = $this->queryBus->handle($query);

            if (empty($hierarchy)) {
                $io->info('No zones found.');
                return Command::SUCCESS;
            }

            $io->title('Zone Hierarchy');

            if ($rootZoneId) {
                $this->renderTree($io, $hierarchy, 0);
            } else {
                foreach ($hierarchy as $rootNode) {
                    $this->renderTree($io, $rootNode, 0);
                }
            }

            return Command::SUCCESS;
        } catch (DomainException $de) {
            $io->error($this->exceptionMessageManager->cliResponseFormat($de));

            return Command::FAILURE;
        } catch (\Exception $e) {
            $io->error("Failed: {$e->getMessage()}");
            return Command::FAILURE;
        }
    }

    private function renderTree(SymfonyStyle $io, array $node, int $level): void
    {
        $indent = str_repeat('  ', $level);
        $prefix = $level > 0 ? '└─ ' : '';

        $icon = $node['icon'] ?? '';
        $temp = $node['currentTemperature'] ? round($node['currentTemperature'], 1) . '°C' : '';
        $occupied = $node['isOccupied'] ? '✓' : '✗';
        $power = $node['currentPowerConsumption'] ? round($node['currentPowerConsumption'], 0) . 'W' : '';

        $info = '';

        if (!empty($temp)) {
            $info .= $temp;
        }

        $info .= (!empty($info) ? ' | ' : '').$occupied;

        if (!empty($power)) {
            $info .= (!empty($info) ? ' | ' : '').$power;
        }

        $io->writeln(sprintf(
            '%s%s<info>%s %s</info> [%s] %s',
            $indent,
            $prefix,
            $icon,
            $node['name'],
            $node['type'],
            $info ? "[{$info}]" : ''
        ));

        foreach ($node['children'] as $child) {
            $this->renderTree($io, $child, $level + 1);
        }
    }
}
