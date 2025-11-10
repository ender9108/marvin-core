<?php

declare(strict_types=1);

namespace Marvin\Protocol\Infrastructure\Framework\Symfony\Command;

use Exception;
use Marvin\Protocol\Infrastructure\Protocol\WebSocketProtocol;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'protocol:websocket:listen',
    description: 'Listen to WebSocket messages from a server',
)]
final class WebSocketListenCommand
{
    public function __invoke(SymfonyStyle $io, InputInterface $input): int
    {
        $url = $input->getArgument('url');
        $ssl = $input->getOption('ssl');
        $maxMessages = $input->getOption('max-messages') ? (int) $input->getOption('max-messages') : null;
        $timeout = $input->getOption('timeout') ? (float) $input->getOption('timeout') : 5.0;

        // Parse URL
        $parsed = parse_url((string) $url);
        $host = $parsed['host'] ?? 'localhost';
        $port = $parsed['port'] ?? ($ssl ? 443 : 80);
        $path = $parsed['path'] ?? '/';

        $io->info('Starting WebSocket listener');
        $io->writeln(sprintf('Host: %s', $host));
        $io->writeln(sprintf('Max messages: %s', $maxMessages ?? 'infinite'));
        $io->newLine();

        try {
            $websocket = new WebSocketProtocol(
                host: $host,
                port: $port,
                ssl: $ssl,
                path: $path,
                timeout: $timeout
            );

            $websocket->connect();
            $io->success('Connected');

            $messageCount = 0;

            $websocket->listen(
                callback: function ($data) use ($io, &$messageCount): void {
                    $messageCount++;
                    $io->section(sprintf('Message #%d', $messageCount));
                    $io->writeln(json_encode($data, JSON_PRETTY_PRINT));
                },
                maxMessages: $maxMessages,
                timeout: $timeout
            );

            $io->success(sprintf('Received %d message(s)', $messageCount));
            $websocket->disconnect();
        } catch (Exception $e) {
            $io->error(sprintf('Error: %s', $e->getMessage()));
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
            ->addOption(
                'max-messages',
                'm',
                InputOption::VALUE_OPTIONAL,
                'Maximum number of messages to receive (default: infinite)',
                null
            )
            ->addOption(
                'timeout',
                't',
                InputOption::VALUE_OPTIONAL,
                'Timeout per message in seconds',
                '5.0'
            )
        ;
    }
}
