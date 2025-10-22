<?php

namespace Marvin\Device\Presentation\Cli\Command;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandBusInterface;
use Marvin\Device\Application\Command\Group\CreateGroup;
use Marvin\Device\Domain\ValueObject\CompositeStrategy;
use Marvin\Shared\Domain\ValueObject\Identity\ZoneId;
use Marvin\Shared\Domain\ValueObject\Label;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;
use ValueError;

#[AsCommand(
    name: 'marvin:device:create-group',
    description: 'Create a device group (with native optimization if available)',
    help: <<<HELP
marvin:device:create-group command creates a new device group.

    Native optimization:
If all devices use the same protocol (e.g., all Zigbee) and the protocol supports native groups,
Marvin will automatically create a native group for maximum performance (1 command instead of N).

Examples:

  # Create a Zigbee group (will use native if all devices are Zigbee)
  php bin/console marvin:device:create-group "Lumières Salon" \
    --devices=zigbee-lampe1-uuid,zigbee-lampe2-uuid,zigbee-lampe3-uuid

  # Create a mixed group (will use emulation)
  php bin/console marvin:device:create-group "Toutes Lumières" \
    --devices=zigbee-lampe1,wifi-shelly,zwave-lampe

  # Force native only (error if not supported)
  php bin/console marvin:device:create-group "Groupe Zigbee" \
    --devices=... --strategy=native_only

  # Force emulation (even if native available)
  php bin/console marvin:device:create-group "Groupe Émulé" \
    --devices=... --strategy=emulated_only
HELP
)]
final readonly class CreateSceneCommand
{
    public function __construct(
        private SyncCommandBusInterface $syncCommandBus
    ) {
    }

    public function __invoke(
        SymfonyStyle $io,
        #[Argument(name: 'label')]
        string $label,
        #[Option(name: 'devices')]
        string $devices,
        #[Option(name: 'strategy')]
        string $strategy,
        #[Option(name: 'zone')]
        ?string $zoneId = null,
    ): int {
        if (!$devices) {
            $io->error('You must provide at least one device via --devices option');
            return Command::FAILURE;
        }

        $deviceIds = array_filter(array_map('trim', explode(',', $devices)));

        if (empty($deviceIds)) {
            $io->error('No valid device IDs provided');
            return Command::FAILURE;
        }

        // Valider la stratégie
        try {
            $strategy = CompositeStrategy::from($strategy);
        } catch (ValueError $e) {
            $io->error("Invalid strategy: {$strategy}");
            $io->note('Available strategies: ' . implode(', ', array_map(
                fn($case) => $case->value,
                CompositeStrategy::cases()
            )));
            return Command::FAILURE;
        }

        $io->section('Creating device group');
        $io->table(
            ['Property', 'Value'],
            [
                ['Label', $label],
                ['Devices', count($deviceIds)],
                ['Strategy', $strategy->value],
                ['Zone', $zoneId ?? 'None'],
            ]
        );

        // Capabilities par défaut pour un groupe de lumières
        $capabilities = [
            [
                'label' => 'light',
                'type' => 'dimmable_light',
                'actions' => ['turn_on', 'turn_off', 'set_brightness', 'toggle'],
                'states' => ['state', 'brightness'],
            ]
        ];

        try {
            $command = new CreateGroup(
                label: new Label($label),
                childDeviceIds: $deviceIds,
                strategy: $strategy,
                zoneId: null !== $zoneId ? new ZoneId($zoneId) : null,
                capabilities: $capabilities
            );

            $this->syncCommandBus->handle($command);

            $io->success("Group '{$label}' created successfully!");
            $io->note("The system will automatically use native protocol groups if all devices share the same protocol.");

            return Command::SUCCESS;

        } catch (Throwable $e) {
            $io->error("Failed to create group: {$e->getMessage()}");
            return Command::FAILURE;
        }
    }
}
