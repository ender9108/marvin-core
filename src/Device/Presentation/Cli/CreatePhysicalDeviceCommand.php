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

use EnderLab\DddCqrsBundle\Application\Command\CommandBusInterface;
use Exception;
use Marvin\Device\Application\Command\Device\CreatePhysicalDevice;
use Marvin\Device\Domain\ValueObject\DeviceType;
use Marvin\Device\Domain\ValueObject\PhysicalAddress;
use Marvin\Device\Domain\ValueObject\Protocol;
use Marvin\Device\Domain\ValueObject\TechnicalName;
use Marvin\Shared\Domain\ValueObject\Identity\ProtocolId;
use Marvin\Shared\Domain\ValueObject\Label;
use Marvin\Shared\Presentation\Exception\Service\ExceptionMessageManager;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'marvin:device:create-physical',
    description: 'Create a physical device (sensor or actuator)',
)]
final readonly class CreatePhysicalDeviceCommand
{
    public function __construct(
        private CommandBusInterface $commandBus,
        private ExceptionMessageManager $exceptionMessageManager,
    ) {
    }

    public function __invoke(
        SymfonyStyle $io,
        #[Argument(description: 'Device label', name: 'label')]
        string $label,
        #[Argument(description: 'Device type (actuator, sensor)', name: 'type')]
        string $type,
        #[Argument(description: 'Protocol (zigbee, network, zwave, matter, thread, bluetooth)', name: 'protocol')]
        string $protocol,
        #[Argument(description: 'Protocol ID', name: 'protocol-id')]
        string $protocolId,
        #[Argument(description: 'Physical address (e.g., MAC, IEEE address)', name: 'physical-address')]
        string $physicalAddress,
        #[Argument(description: 'Technical name (model/manufacturer)', name: 'technical-name')]
        string $technicalName,
        #[Option(description: 'Capabilities as JSON array (e.g., ["on_off","brightness"])', name: 'capabilities')]
        ?string $capabilities = null,
    ): int {
        try {
            $capabilitiesArray = [];
            if (null !== $capabilities) {
                $decoded = json_decode($capabilities, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $io->error('Invalid JSON capabilities: ' . json_last_error_msg());
                    return Command::FAILURE;
                }
                $capabilitiesArray = $decoded;
            }

            $command = new CreatePhysicalDevice(
                label: Label::fromString($label),
                deviceType: DeviceType::from($type),
                protocol: Protocol::from($protocol),
                protocolId: new ProtocolId($protocolId),
                physicalAddress: PhysicalAddress::fromString($physicalAddress),
                technicalName: TechnicalName::fromString($technicalName),
                capabilities: $capabilitiesArray,
            );

            $this->commandBus->dispatch($command);

            $io->success(sprintf('Physical device "%s" created successfully.', $label));

            return Command::SUCCESS;
        } catch (Exception $e) {
            $io->error($this->exceptionMessageManager->cliResponseFormat($e));

            return Command::FAILURE;
        }
    }
}
