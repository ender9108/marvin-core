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
use Marvin\Protocol\Infrastructure\Listener\MqttDeviceStateListener;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'protocol:mqtt:listen',
    description: 'Listen to MQTT messages and transform them to domain events',
)]
final readonly class MqttListenCommand
{
    public function __construct(
        private MqttDeviceStateListener $listener,
    ) {
    }

    public function __invoke(
        SymfonyStyle $io,
        #[Option(name: 'topics')]
        ?string $topics = null,
        #[Option(name: 'timeout')]
        ?int $timeout = null,
    ): int {
        $topicsArray = $topics ? explode(',', $topics) : ['#'];
        $timeoutInt = $timeout ?: null;

        $io->info('Starting MQTT Listener');
        $io->writeln(sprintf('Topics: %s', implode(', ', $topicsArray)));
        $io->writeln(sprintf('Timeout: %s', $timeoutInt ? $timeoutInt . 's' : 'infinite'));
        $io->newLine();

        try {
            $this->listener->listen($topicsArray, $timeoutInt);
            $io->success('MQTT Listener stopped gracefully');
        } catch (Exception $e) {
            $io->error(sprintf('Error: %s', $e->getMessage()));
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
