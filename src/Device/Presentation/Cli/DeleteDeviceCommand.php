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
use Marvin\Device\Application\Command\Device\DeleteDevice;
use Marvin\Shared\Domain\ValueObject\Identity\DeviceId;
use Marvin\Shared\Presentation\Exception\Service\ExceptionMessageManager;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'marvin:device:delete',
    description: 'Delete a device',
)]
final readonly class DeleteDeviceCommand
{
    public function __construct(
        private SyncCommandBusInterface $syncCommandBus,
        private ExceptionMessageManager $exceptionMessageManager,
    ) {
    }

    public function __invoke(
        SymfonyStyle $io,
        #[Argument(description: 'Device ID', name: 'device-id')]
        string $deviceId,
    ): int {
        try {
            if (!$io->confirm(sprintf('Are you sure you want to delete the device "%s"?', $deviceId), false)) {
                $io->info('Operation cancelled.');
                return Command::SUCCESS;
            }

            $this->syncCommandBus->handle(new DeleteDevice(
                deviceId: new DeviceId($deviceId),
            ));

            $io->success(sprintf('Device %s deleted successfully.', $deviceId));

            return Command::SUCCESS;
        } catch (Exception $e) {
            $io->error($this->exceptionMessageManager->cliResponseFormat($e));

            return Command::FAILURE;
        }
    }
}
