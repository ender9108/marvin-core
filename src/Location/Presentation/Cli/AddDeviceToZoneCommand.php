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

namespace Marvin\Location\Presentation\Cli;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandBusInterface;
use Exception;
use Marvin\Location\Application\Command\Zone\AddDeviceToZone;
use Marvin\Shared\Domain\ValueObject\Identity\DeviceId;
use Marvin\Shared\Domain\ValueObject\Identity\ZoneId;
use Marvin\Shared\Presentation\Exception\Service\ExceptionMessageManager;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'marvin:location:add-device-to-zone',
    description: 'Add a device to a zone',
)]
final readonly class AddDeviceToZoneCommand
{
    public function __construct(
        private SyncCommandBusInterface $syncCommandBus,
        private ExceptionMessageManager $exceptionMessageManager,
    ) {
    }

    public function __invoke(
        SymfonyStyle $io,
        #[Argument(description: 'Zone ID', name: 'zone-id')]
        string $zoneId,
        #[Argument(description: 'Device ID', name: 'device-id')]
        string $deviceId,
    ): int {
        try {
            $this->syncCommandBus->handle(new AddDeviceToZone(
                zoneId: new ZoneId($zoneId),
                deviceId: new DeviceId($deviceId),
            ));

            $io->success(sprintf('Device %s added to zone %s successfully.', $deviceId, $zoneId));

            return Command::SUCCESS;
        } catch (Exception $e) {
            $io->error($this->exceptionMessageManager->cliResponseFormat($e));

            return Command::FAILURE;
        }
    }
}
