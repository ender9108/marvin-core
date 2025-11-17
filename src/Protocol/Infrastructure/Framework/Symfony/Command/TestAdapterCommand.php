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

namespace Marvin\Protocol\Infrastructure\Framework\Symfony\Command;

use Exception;
use Marvin\Protocol\Domain\Model\ProtocolAdapterInterface;
use Marvin\Protocol\Domain\ValueObject\ExecutionMode;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'protocol:adapter:test',
    description: 'Test a protocol adapter by sending a command',
)]
final class TestAdapterCommand
{
    use HandleTrait;

    /**
     * @param iterable<ProtocolAdapterInterface> $adapters
     */
    public function __construct(
        MessageBusInterface $messageBus,
        #[AutowireIterator(tag: 'protocol.adapter')]
        private readonly iterable $adapters,
    ) {
        $this->messageBus = $messageBus;
    }

    public function __invoke(
        SymfonyStyle $io,
        #[Argument(description: 'Adapter name (e.g., zigbee2mqtt, shelly_gen2)', name: 'adapter')]
        string $adapterName,
        #[Argument(description: 'Device ID or native ID', name: 'device-id')]
        string $deviceId,
        #[Argument(description: 'Action to perform (e.g., turn_on, turn_off)', name: 'action')]
        string $action,
        #[Option(description: 'JSON parameters for the action', name: 'parameters')]
        ?string $parametersJson,
        #[Option(description: 'Execution mode (correlation_id, device_lock, fire_and_forget)', name: 'mode')]
        ?string $mode,
    ): int {
        // Find adapter by name
        $adapter = null;
        foreach ($this->adapters as $candidate) {
            if ($candidate->getName() === $adapterName) {
                $adapter = $candidate;
                break;
            }
        }

        if ($adapter === null) {
            $io->error(sprintf('Adapter "%s" not found', $adapterName));

            // List available adapters
            $io->writeln('Available adapters:');
            foreach ($this->adapters as $candidate) {
                $io->writeln(sprintf('  - %s', $candidate->getName()));
            }

            return Command::FAILURE;
        }

        // Parse parameters
        $parameters = [];
        if ($parametersJson) {
            $parameters = json_decode($parametersJson, true);
            if ($parameters === null) {
                $io->error('Invalid JSON parameters');
                return Command::FAILURE;
            }
        }

        // Parse execution mode
        $executionMode = $mode ? ExecutionMode::from($mode) : $adapter->getDefaultExecutionMode();

        $io->info('Testing adapter');
        $io->writeln(sprintf('Adapter: %s', $adapter->getName()));
        $io->writeln(sprintf('Device ID: %s', $deviceId));
        $io->writeln(sprintf('Action: %s', $action));
        $io->writeln(sprintf('Parameters: %s', json_encode($parameters)));
        $io->writeln(sprintf('Execution Mode: %s', $executionMode->value));
        $io->newLine();

        try {
            $result = $adapter->sendCommand(
                $deviceId,
                $action,
                $parameters,
                $executionMode
            );

            if ($result === null) {
                $io->success('Command sent (FIRE_AND_FORGET mode)');
            } else {
                $io->success('Command sent successfully');
                $io->writeln('Result:');
                $io->writeln(json_encode($result, JSON_PRETTY_PRINT));
            }
        } catch (Exception $e) {
            $io->error(sprintf('Error: %s', $e->getMessage()));
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
