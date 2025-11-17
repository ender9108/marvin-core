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
use Marvin\Device\Application\Command\Device\ExecuteDeviceAction;
use Marvin\Device\Domain\ValueObject\Capability;
use Marvin\Device\Domain\ValueObject\CapabilityAction;
use Marvin\Shared\Domain\ValueObject\Identity\DeviceId;
use Marvin\Shared\Presentation\Exception\Service\ExceptionMessageManager;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'marvin:device:execute-action',
    description: 'Execute an action on a device',
)]
final readonly class ExecuteDeviceActionCommand
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
        #[Argument(description: 'Capability (e.g., on_off, brightness, color)', name: 'capability')]
        string $capability,
        #[Argument(description: 'Action (e.g., turn_on, turn_off, set)', name: 'action')]
        string $action,
        #[Option(description: 'Action parameters as JSON (e.g., {"brightness":50})', name: 'parameters')]
        ?string $parameters = null,
    ): int {
        try {
            $params = [];
            if (null !== $parameters) {
                $decoded = json_decode($parameters, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $io->error('Invalid JSON parameters: ' . json_last_error_msg());
                    return Command::FAILURE;
                }
                $params = $decoded;
            }

            $this->syncCommandBus->handle(new ExecuteDeviceAction(
                deviceId: new DeviceId($deviceId),
                capability: Capability::from($capability),
                action: CapabilityAction::from($action),
                parameters: $params,
            ));

            $io->success(sprintf(
                'Action %s/%s executed on device %s successfully.',
                $capability,
                $action,
                $deviceId
            ));

            return Command::SUCCESS;
        } catch (Exception $e) {
            $io->error($this->exceptionMessageManager->cliResponseFormat($e));

            return Command::FAILURE;
        }
    }
}
