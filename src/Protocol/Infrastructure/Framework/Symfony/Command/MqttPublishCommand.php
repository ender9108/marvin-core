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
use Marvin\Protocol\Infrastructure\Service\MqttPublisher;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'protocol:mqtt:publish',
    description: 'Publish a message to an MQTT topic',
)]
final readonly class MqttPublishCommand
{
    public function __construct(
        private MqttPublisher $mqttPublisher,
    ) {
    }

    public function __invoke(
        SymfonyStyle $io,
        #[Argument(name: 'topic')]
        string $topic,
        #[Argument(name: 'message')]
        string $message,
        #[Option(name: 'correlation-id')]
        ?string $correlationId = null,
    ): int {
        $io->info('Publishing MQTT message');
        $io->writeln(sprintf('Topic: %s', $topic));
        $io->writeln(sprintf('Message: %s', $message));

        if ($correlationId) {
            $io->writeln(sprintf('Correlation ID: %s', $correlationId));
        }

        $io->newLine();

        try {
            // Try to decode JSON message
            $decoded = json_decode($message, true);
            $payload = $decoded ?? $message;

            if ($correlationId) {
                $this->mqttPublisher->publishWithCorrelation(
                    $topic,
                    $payload,
                    $correlationId
                );
            } else {
                $this->mqttPublisher->publish($topic, $payload);
            }

            $io->success('Message published successfully');
        } catch (Exception $e) {
            $io->error(sprintf('Error: %s', $e->getMessage()));
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
