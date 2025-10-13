<?php

namespace Marvin\System\Infrastructure\Framework\Symfony\Service;

use Marvin\System\Domain\Exception\MqttLoopError;
use Marvin\System\Domain\Exception\MqttPublishError;
use Marvin\System\Domain\Exception\MqttSubscribeError;
use Psr\Log\LoggerInterface;
use Simps\MQTT\Client;
use Simps\MQTT\Config\ClientConfig;
use Simps\MQTT\Protocol\Types;
use Throwable;

class MqttClientService
{
    private Client $client;
    private int $lastPingAt;
    private bool $running = false;

    public function __construct(
        private readonly string $host,
        private readonly int $port,
        private readonly ClientConfig $config,
        private readonly LoggerInterface $logger,
    ) {
        // The underlying Simps client connects to the socket in its constructor.
        // It requires a Swoole context when using coroutine client type (default).
        $this->client = new Client($this->host, $this->port, $this->config);
        $this->lastPingAt = time();
    }

    /**
     * Establish the MQTT connection (sends CONNECT) with optional clean session and will message.
     */
    public function connect(bool $cleanSession = true, array $will = []): void
    {
        try {
            $this->client->connect($cleanSession, $will);
            $this->logger->info('MQTT connected', [
                'host' => $this->host,
                'port' => $this->port,
                'client_id' => $this->config->getClientId(),
            ]);
            $this->lastPingAt = time();
        } catch (Throwable $e) {
            $this->logger->error('MQTT connect error: ' . $e->getMessage(), ['exception' => $e]);
            throw $e;
        }
    }

    /**
     * Subscribe to topics. $topics is an associative array: [topic => qos].
     */
    public function subscribe(array $topics): void
    {
        try {
            $this->client->subscribe($topics);
            $this->logger->info('MQTT subscribed', ['topics' => array_keys($topics)]);
        } catch (Throwable $e) {
            $this->logger->error('MQTT subscribe error: ' . $e->getMessage(), ['exception' => $e, 'topics' => $topics]);
            throw MqttSubscribeError::withTopic($topics);
        }
    }

    /**
     * Publish a message.
     * Returns the response for QoS > 0 according to simps/mqtt Client::publish.
     */
    public function publish(string $topic, string $payload, int $qos = 0, bool $retain = false): mixed
    {
        try {
            return $this->client->publish($topic, $payload, $qos, $retain ? 1 : 0);
        } catch (Throwable $e) {
            $this->logger->error('MQTT publish error: ' . $e->getMessage(), [
                'exception' => $e,
                'topic' => $topic,
                'qos' => $qos,
                'retain' => $retain,
            ]);
            throw MqttPublishError::withParameters($topic, $qos, $retain);
        }
    }

    /**
     * Start a simple receive loop and invoke $onMessage for incoming PUBLISH packets.
     * The callback signature is: function(array $message): void
     * Use stop() to break the loop from outside.
     */
    public function loop(callable $onMessage): void
    {
        $this->running = true;
        $keepAlive = $this->client->getConfig()->getKeepAlive();

        while ($this->running) {
            try {
                $buffer = $this->client->recv();
                if ($buffer && $buffer !== true) {
                    // QoS1 PUBACK handling
                    if (($buffer['type'] ?? null) === Types::PUBLISH) {
                        if (($buffer['qos'] ?? 0) === 1 && isset($buffer['message_id'])) {
                            $this->client->send([
                                'type' => Types::PUBACK,
                                'message_id' => $buffer['message_id'],
                            ], false);
                        }

                        // Dispatch to user callback
                        $onMessage($buffer);
                    }

                    // Broker initiated disconnect
                    if (($buffer['type'] ?? null) === Types::DISCONNECT) {
                        $this->logger->warning('MQTT broker disconnected');
                        $this->running = false;
                        break;
                    }
                }

                // KeepAlive ping
                if ($this->lastPingAt <= (time() - $keepAlive)) {
                    $pong = $this->client->ping();
                    if ($pong) {
                        $this->logger->debug('MQTT ping sent');
                        $this->lastPingAt = time();
                    }
                }
            } catch (Throwable $e) {
                // recv() will auto-reconnect if needed; still log errors
                $this->logger->error('MQTT loop error: ' . $e->getMessage(), ['exception' => $e]);
                throw MqttLoopError::withError($e->getMessage());
            }
        }
    }

    /** Stop the receive loop started by loop(). */
    public function stop(): void
    {
        $this->running = false;
    }

    /** Close the connection. */
    public function disconnect(): void
    {
        try {
            $this->running = false;
            $this->client->close();
            $this->logger->info('MQTT connection closed');
        } catch (Throwable $e) {
            $this->logger->warning('MQTT close error: ' . $e->getMessage(), ['exception' => $e]);
        }
    }

    public function getClient(): Client
    {
        return $this->client;
    }
}
