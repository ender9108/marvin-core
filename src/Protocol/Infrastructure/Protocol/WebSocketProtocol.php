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

namespace Marvin\Protocol\Infrastructure\Protocol;

use RuntimeException;
use Swoole\Coroutine\Http\Client as SwooleWebSocketClient;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * WebSocket Protocol Implementation using Swoole
 * Supports persistent bidirectional connections with WSS support
 */
final class WebSocketProtocol
{
    private ?SwooleWebSocketClient $client = null;
    private bool $connected = false;

    public function __construct(
        #[Autowire('%env(WEBSOCKET_HOST)%')]
        private readonly string $host,
        #[Autowire('%env(WEBSOCKET_PORT)%')]
        private readonly int $port = 80,
        #[Autowire('%env(WEBSOCKET_SSL)%')]
        private readonly bool $ssl = false,
        private readonly float $timeout = 5.0,
        private readonly array $headers = [],
        private readonly string $path = '/',
    ) {
    }

    /**
     * Connect to WebSocket server
     *
     * @throws RuntimeException If connection fails
     */
    public function connect(): void
    {
        if ($this->connected) {
            return;
        }

        $this->client = new SwooleWebSocketClient($this->host, $this->port, $this->ssl);
        $this->client->set([
            'timeout' => $this->timeout,
        ]);

        if (!empty($this->headers)) {
            $this->client->setHeaders($this->headers);
        }

        $result = $this->client->upgrade($this->path);

        if (!$result) {
            throw new RuntimeException(
                sprintf(
                    'Failed to connect to WebSocket server %s:%d - Error: %s',
                    $this->host,
                    $this->port,
                    $this->client->errMsg ?? 'Unknown error'
                )
            );
        }

        $this->connected = true;
    }

    /**
     * Disconnect from WebSocket server
     */
    public function disconnect(): void
    {
        if (!$this->connected || $this->client === null) {
            return;
        }

        $this->client->close();
        $this->connected = false;
        $this->client = null;
    }

    /**
     * Send message to WebSocket server
     *
     * @param string|array $data Message data (will be JSON encoded if array)
     * @param int $opcode WebSocket opcode (WEBSOCKET_OPCODE_TEXT = 1 or WEBSOCKET_OPCODE_BINARY = 2)
     * @throws RuntimeException If not connected or send fails
     */
    public function send(string|array $data, int $opcode = WEBSOCKET_OPCODE_TEXT): void
    {
        $this->ensureConnected();

        $message = is_array($data) ? json_encode($data) : $data;

        $result = $this->client->push($message, $opcode);

        if ($result === false) {
            throw new RuntimeException(
                sprintf('Failed to send message: %s', $this->client->errMsg ?? 'Unknown error')
            );
        }
    }

    /**
     * Receive message from WebSocket server
     *
     * @param float|null $timeout Timeout in seconds (null uses default)
     * @return array|string|null Received data (null on timeout or error)
     */
    public function receive(?float $timeout = null): array|string|null
    {
        $this->ensureConnected();

        if ($timeout !== null) {
            $this->client->set(['timeout' => $timeout]);
        }

        $frame = $this->client->recv($timeout ?? $this->timeout);

        if ($frame === false) {
            return null;
        }

        // Try to decode JSON
        if (is_string($frame->data ?? $frame)) {
            $decoded = json_decode($frame->data ?? $frame, true);
            return $decoded ?? $frame->data ?? $frame;
        }

        return $frame->data ?? $frame;
    }

    /**
     * Send message and wait for response
     *
     * @param string|array $data Message data
     * @param float|null $timeout Timeout in seconds
     * @return array|string|null Response data
     */
    public function sendAndWait(string|array $data, ?float $timeout = null): array|string|null
    {
        $this->send($data);
        return $this->receive($timeout);
    }

    /**
     * Listen for messages with callback
     *
     * @param callable $callback Callback function (receives message data)
     * @param int|null $maxMessages Maximum number of messages to receive (null for infinite)
     * @param float|null $timeout Timeout per message in seconds
     */
    public function listen(callable $callback, ?int $maxMessages = null, ?float $timeout = null): void
    {
        $this->ensureConnected();

        $count = 0;

        while (true) {
            $data = $this->receive($timeout);

            if ($data === null) {
                break;
            }

            $callback($data);

            $count++;

            if ($maxMessages !== null && $count >= $maxMessages) {
                break;
            }
        }
    }

    /**
     * Check if connected to server
     */
    public function isConnected(): bool
    {
        return $this->connected && $this->client !== null;
    }

    /**
     * Get connection info
     */
    public function getInfo(): array
    {
        return [
            'host' => $this->host,
            'port' => $this->port,
            'ssl' => $this->ssl,
            'connected' => $this->connected,
        ];
    }

    /**
     * Ensure connection is established
     *
     * @throws RuntimeException If not connected
     */
    private function ensureConnected(): void
    {
        if (!$this->connected || $this->client === null) {
            throw new RuntimeException('Not connected to WebSocket server');
        }
    }

    public function __destruct()
    {
        $this->disconnect();
    }
}
