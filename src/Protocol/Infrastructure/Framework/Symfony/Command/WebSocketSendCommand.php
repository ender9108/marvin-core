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
    name: 'protocol:websocket:send',
    description: 'Send a message to a WebSocket server and optionally wait for response',
)]
final class WebSocketSendCommand
{
    public function __invoke(SymfonyStyle $io, InputInterface $input): int
    {
        $url = $input->getArgument('url');
        $message = $input->getArgument('message');
        $ssl = $input->getOption('ssl');
        $wait = $input->getOption('wait');
        $timeout = $input->getOption('timeout') ? (float) $input->getOption('timeout') : 5.0;

        // Parse URL
        $parsed = parse_url((string) $url);
        $host = $parsed['host'] ?? 'localhost';
        $port = $parsed['port'] ?? ($ssl ? 443 : 80);
        $path = $parsed['path'] ?? '/';

        $io->info('Connecting to WebSocket server');
        $io->writeln(sprintf('Host: %s', $host));
        $io->writeln(sprintf('Message: %s', $message));
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

            // Try to decode JSON message
            $decoded = json_decode((string) $message, true);
            $payload = $decoded ?? $message;

            if ($wait) {
                $io->writeln('Sending message and waiting for response...');
                $response = $websocket->sendAndWait($payload, $timeout);

                $io->success('Response received:');
                $io->writeln(json_encode($response, JSON_PRETTY_PRINT));
            } else {
                $websocket->send($payload);
                $io->success('Message sent');
            }

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
            ->addArgument(
                'message',
                InputArgument::REQUIRED,
                'Message to send (JSON or plain text)'
            )
            ->addOption(
                'ssl',
                's',
                InputOption::VALUE_NONE,
                'Use WSS (secure WebSocket)'
            )
            ->addOption(
                'wait',
                'w',
                InputOption::VALUE_NONE,
                'Wait for response'
            )
            ->addOption(
                'timeout',
                't',
                InputOption::VALUE_OPTIONAL,
                'Timeout in seconds',
                '5.0'
            )
        ;
    }
}
