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
use Marvin\Device\Application\Command\Device\CreateVirtualDevice;
use Marvin\Device\Domain\ValueObject\VirtualDeviceConfig;
use Marvin\Device\Domain\ValueObject\VirtualDeviceType;
use Marvin\Shared\Domain\ValueObject\Label;
use Marvin\Shared\Presentation\Exception\Service\ExceptionMessageManager;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'marvin:device:create-virtual',
    description: 'Create a virtual device (time, weather, http)',
)]
final readonly class CreateVirtualDeviceCommand
{
    public function __construct(
        private SyncCommandBusInterface $syncCommandBus,
        private ExceptionMessageManager $exceptionMessageManager,
    ) {
    }

    public function __invoke(
        SymfonyStyle $io,
        #[Argument(description: 'Device label', name: 'label')]
        string $label,
        #[Argument(description: 'Virtual type (time, weather, http)', name: 'virtual-type')]
        string $virtualType,
        #[Argument(description: 'Virtual config as JSON', name: 'virtual-config')]
        string $virtualConfig,
        #[Option(description: 'Capabilities as JSON array (e.g., ["temperature","humidity"])', name: 'capabilities')]
        ?string $capabilities = null,
    ): int {
        try {
            $configArray = json_decode($virtualConfig, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $io->error('Invalid JSON config: ' . json_last_error_msg());
                return Command::FAILURE;
            }

            $capabilitiesArray = [];
            if (null !== $capabilities) {
                $decoded = json_decode($capabilities, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $io->error('Invalid JSON capabilities: ' . json_last_error_msg());
                    return Command::FAILURE;
                }
                $capabilitiesArray = $decoded;
            }

            $deviceId = $this->syncCommandBus->handle(new CreateVirtualDevice(
                label: Label::fromString($label),
                virtualType: VirtualDeviceType::from($virtualType),
                virtualConfig: VirtualDeviceConfig::fromArray($configArray),
                capabilities: $capabilitiesArray,
            ));

            $io->success(sprintf('Virtual device "%s" created successfully with ID: %s', $label, $deviceId));

            return Command::SUCCESS;
        } catch (Exception $e) {
            $io->error($this->exceptionMessageManager->cliResponseFormat($e));

            return Command::FAILURE;
        }
    }
}
