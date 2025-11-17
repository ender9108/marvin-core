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

use Simps\MQTT\Client;
use Simps\MQTT\Config\ClientConfig;
use Simps\MQTT\Hex\ReasonCode;
use Simps\MQTT\Protocol\Types;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * MQTT Protocol Implementation using simps/mqtt library
 * Supports MQTT v5 with correlation data, response topics, and user properties
 */
final class MqttProtocol
{
    private ?Client $client = null;
    private ClientConfig $config;

    public function __construct(
        #[Autowire('%env(MQTT_HOST)%')]
        private readonly string $host,
        #[Autowire('%env(MQTT_PORT)%')]
        private readonly int $port = 1883,
        #[Autowire('%env(MQTT_USER)%')]
        private readonly ?string $username = null,
        #[Autowire('%env(MQTT_PASSWORD)%')]
        private readonly ?string $password = null,
        #[Autowire('%env(MQTT_PROTOCOL_LEVEL)%')]
        private readonly int $protocolLevel = 5,
        #[Autowire('%env(MQTT_USE_SSL)%')]
        private readonly bool $ssl = false,
        #[Autowire('%env(MQTT_SSL_ALLOW_SELF_SIGNED)%')]
        private readonly bool $sslAllowSelfSigned = false,
        #[Autowire('%env(MQTT_SSL_VERIFY_PEER)%')]
        private readonly bool $sslVerifyPeer = false,
        #[Autowire('%env(MQTT_SSL_CERT_FILE)%')]
        private readonly string $sslCaFile = '',
        #[Autowire('%env(MQTT_SSL_CERT_FILE)%')]
        private readonly string $sslCertFile = '',
        #[Autowire('%env(MQTT_SSL_KEY_FILE)%')]
        private readonly string $sslKeyFile = '',
        private readonly int $qos = 1,
        private readonly bool $retain = false,
        private readonly int $keepAlive = 60,
    ) {
        $this->initializeConfig();
    }

    private function initializeConfig(): void
    {
        $config = [
            'host' => $this->host,
            'port' => $this->port,
            'protocol_level' => $this->protocolLevel,
            'keepalive' => $this->keepAlive,
            'client_id' => 'marvin_' . uniqid(),
            'properties' => [
                'session_expiry_interval' => 3600,
            ],
        ];

        if ($this->username !== null) {
            $config['username'] = $this->username;
        }

        if ($this->password !== null) {
            $config['password'] = $this->password;
        }

        if ($this->ssl) {
            $config['ssl_allow_self_signed'] = $this->sslAllowSelfSigned;
            $config['ssl_verify_peer'] = $this->sslVerifyPeer;
            $config['ssl_cafile'] = $this->sslCaFile;
            $config['ssl_key_file'] = $this->sslKeyFile;
            $config['ssl_cert_file'] = $this->sslCertFile;
        }

        $this->config = new ClientConfig($config);
    }

    /**
     * Connect to MQTT broker
     */
    public function connect(): void
    {
        if ($this->client !== null) {
            return;
        }

        $this->client = new Client(
            $this->host,
            $this->port,
            $this->config
        );
        $this->client->connect(true);
    }

    /**
     * Disconnect from MQTT broker
     */
    public function disconnect(): void
    {
        if ($this->client === null) {
            return;
        }

        $this->client->close();
        $this->client = null;
    }

    /**
     * Publish a message to a topic
     *
     * @param string $topic MQTT topic
     * @param array|string $payload Message payload (will be JSON encoded if array)
     * @param array $properties MQTT v5 properties (correlation_data, response_topic, user_properties)
     */
    public function publish(string $topic, array|string $payload, array $properties = [], ?int $qos = null): void
    {
        $this->ensureConnected();

        $message = is_array($payload) ? json_encode($payload) : $payload;

        $this->client->publish(
            $topic,
            $message,
            $qos ?? $this->qos,
            0,
            $this->retain ? 1 : 0,
            $properties
        );
    }

    /**
     * Subscribe to one or more topics
     *
     * @param array|string $topics Topic or array of topics
     */
    public function subscribe(array|string $topics): void
    {
        $this->ensureConnected();

        $topics = is_array($topics) ? $topics : [$topics];

        foreach ($topics as $topic) {
            $this->client->subscribe([
                $topic => ['qos' => $this->qos],
            ]);
        }
    }

    /**
     * Unsubscribe from topics
     *
     * @param array|string $topics Topic or array of topics
     */
    public function unsubscribe(array|string $topics): void
    {
        $this->ensureConnected();

        $topics = is_array($topics) ? $topics : [$topics];
        $this->client->unsubscribe($topics);
    }

    /**
     * Receive messages (blocking)
     *
     * @param callable $callback Callback function to handle received messages
     * @param int|null $timeout Timeout in seconds (null for infinite)
     */
    public function receive(callable $callback, ?int $timeout = null): void
    {
        $this->ensureConnected();

        $timeSincePing = time();

        while (true) {
            $buffer = $this->client->recv();

            if ($buffer && $buffer !== true) {
                var_dump($buffer);
                // QoS1 PUBACK
                if ($buffer['type'] === Types::PUBLISH && $buffer['qos'] === 1) {
                    $this->client->send(
                        [
                            'type' => Types::PUBACK,
                            'message_id' => $buffer['message_id'],
                        ],
                        false
                    );
                }

                if ($buffer['type'] === Types::DISCONNECT) {
                    echo sprintf(
                        "Broker is disconnected, The reason is %s [%d]\n",
                        ReasonCode::getReasonPhrase($buffer['code']),
                        $buffer['code']
                    );

                    $this->client->close($buffer['code']);
                    break;
                }

                if ($buffer['type'] === Types::PUBLISH) {
                    $topic = $buffer['topic'] ?? '';
                    $payload = $buffer['message'] ?? '';
                    $properties = $buffer['properties'] ?? [];

                    // Try to decode JSON payload
                    $decodedPayload = json_decode((string) $payload, true);
                    $payload = $decodedPayload ?? $payload;

                    $callback($topic, $payload, $properties);
                }
            }

            if ($timeSincePing <= (time() - $this->client->getConfig()->getKeepAlive())) {
                $buffer = $this->client->ping();

                if ($buffer) {
                    echo 'send ping success' . PHP_EOL;
                    $timeSincePing = time();
                }
            }


            /*$message = $this->client->recv();

            if (!is_array($message)) {
                break;
            }

            if (isset($message['type']) && $message['type'] === 'publish') {
                $topic = $message['topic'] ?? '';
                $payload = $message['message'] ?? '';
                $properties = $message['properties'] ?? [];

                // Try to decode JSON payload
                $decodedPayload = json_decode((string) $payload, true);
                $payload = $decodedPayload ?? $payload;

                $callback($topic, $payload, $properties);
            }

            // Check timeout
            if ($timeout !== null && (time() - $startTime) >= $timeout) {
                break;
            }*/
        }
    }

    /**
     * Check if connected to broker
     */
    public function isConnected(): bool
    {
        return $this->client !== null;
    }

    /**
     * Get client configuration
     */
    public function getConfig(): ClientConfig
    {
        return $this->config;
    }

    /**
     * Ensure connection is established
     */
    private function ensureConnected(): void
    {
        if ($this->client === null) {
            $this->connect();
        }
    }

    public function __destruct()
    {
        $this->disconnect();
    }
}
