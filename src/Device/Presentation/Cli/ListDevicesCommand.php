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
use Marvin\Device\Application\Query\Device\GetDeviceCollection;
use Marvin\Device\Domain\Model\Device;
use Marvin\Shared\Presentation\Exception\Service\ExceptionMessageManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'marvin:device:list',
    description: 'List all devices',
)]
final readonly class ListDevicesCommand
{
    public function __construct(
        private QueryBusInterface $queryBus,
        private ExceptionMessageManager $exceptionMessageManager,
    ) {
    }

    public function __invoke(
        SymfonyStyle $io,
        #[Option(description: 'Filter by device type', name: 'type')]
        ?string $type = null,
        #[Option(description: 'Filter by protocol', name: 'protocol')]
        ?string $protocol = null,
        #[Option(description: 'Filter by status', name: 'status')]
        ?string $status = null,
    ): int {
        try {
            $filters = [];
            if (null !== $type) {
                $filters['type'] = $type;
            }
            if (null !== $protocol) {
                $filters['protocol'] = $protocol;
            }
            if (null !== $status) {
                $filters['status'] = $status;
            }

            /** @var array<Device> $devices */
            $devices = $this->queryBus->handle(new GetDeviceCollection(filters: $filters));

            if (empty($devices)) {
                $io->info('No devices found.');
                return Command::SUCCESS;
            }

            $rows = [];
            foreach ($devices as $device) {
                $rows[] = [
                    $device->id->toString(),
                    $device->label->value,
                    $device->deviceType->value,
                    $device->compositeType?->value ?? 'N/A',
                    $device->protocol?->value ?? 'N/A',
                    $device->status->value,
                    $device->technicalName?->value ?? 'N/A',
                    count($device->capabilities),
                ];
            }

            $io->table(
                ['ID', 'Label', 'Type', 'Composite', 'Protocol', 'Status', 'Technical Name', 'Capabilities'],
                $rows
            );

            $io->success(sprintf('Found %d device(s).', count($devices)));

            return Command::SUCCESS;
        } catch (Exception $e) {
            $io->error($this->exceptionMessageManager->cliResponseFormat($e));

            return Command::FAILURE;
        }
    }
}
