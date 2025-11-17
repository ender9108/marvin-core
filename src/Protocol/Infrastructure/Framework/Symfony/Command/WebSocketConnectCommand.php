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
use Marvin\Protocol\Infrastructure\Protocol\WebSocketProtocol;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'protocol:websocket:connect',
    description: 'Test WebSocket connection to a device',
)]
final class WebSocketConnectCommand
{
    public function __invoke(
        SymfonyStyle $io,
        #[Argument(description: 'WebSocket URL (e.g., ws://192.168.1.100/rpc)', name: 'url')]
        string $url,
        #[Option(description: 'Use WSS (secure WebSocket)', name: 'ssl')]
        bool $ssl = false,
    ): int {
        // Parse URL
        $parsed = parse_url($url);
        $host = $parsed['host'] ?? 'localhost';
        $port = $parsed['port'] ?? ($ssl ? 443 : 80);
        $path = $parsed['path'] ?? '/';

        $io->info('Connecting to WebSocket server');
        $io->writeln(sprintf('Host: %s', $host));
        $io->writeln(sprintf('Port: %d', $port));
        $io->writeln(sprintf('Path: %s', $path));
        $io->writeln(sprintf('SSL: %s', $ssl ? 'yes' : 'no'));
        $io->newLine();

        try {
            $websocket = new WebSocketProtocol(
                host: $host,
                port: $port,
                ssl: $ssl,
                path: $path
            );

            $websocket->connect();

            $io->success('Connected successfully!');
            $io->writeln('Connection info:');
            $io->listing(array_map(
                fn ($key, $value) => sprintf('%s: %s', $key, json_encode($value)),
                array_keys($websocket->getInfo()),
                $websocket->getInfo()
            ));

            $websocket->disconnect();
        } catch (Exception $e) {
            $io->error(sprintf('Connection failed: %s', $e->getMessage()));
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    public function configure(): void
    {
        $this
            ->addArgument(
                'url',
                InputArgument::REQUIRED,
                'WebSocket URL (e.g., ws://192.168.1.100/rpc)'
            )
            ->addOption(
                'ssl',
                's',
                InputOption::VALUE_NONE,
                'Use WSS (secure WebSocket)'
            )
        ;
    }
}
