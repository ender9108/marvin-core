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

namespace Marvin\Protocol\Infrastructure\Service;

use Marvin\Protocol\Infrastructure\Protocol\MqttProtocol;
use Marvin\Shared\Application\Service\Acl\SecretQueryServiceInterface;
use RuntimeException;

/**
 * MQTT Publisher Service
 * Provides a simple interface to publish messages to MQTT topics
 */
final class MqttPublisher
{
    private ?MqttProtocol $mqttProtocol = null;

    public function __construct(
        private readonly SecretQueryServiceInterface $secretQueryService,
    ) {
    }

    /**
     * Publish a message to an MQTT topic
     *
     * @param string $topic MQTT topic
     * @param array|string $payload Message payload
     * @param array $properties MQTT v5 properties (correlation_data, response_topic, etc.)
     */
    public function publish(string $topic, array|string $payload, array $properties = []): void
    {
        $mqtt = $this->getMqttProtocol();
        $mqtt->publish($topic, $payload, $properties);
    }

    /**
     * Publish with correlation ID (MQTT v5)
     *
     * @param string $topic MQTT topic
     * @param array|string $payload Message payload
     * @param string $correlationId Correlation ID
     * @param string|null $responseTopic Optional response topic
     */
    public function publishWithCorrelation(
        string $topic,
        array|string $payload,
        string $correlationId,
        ?string $responseTopic = null
    ): void {
        $properties = [
            'correlation_data' => $correlationId,
        ];

        if ($responseTopic !== null) {
            $properties['response_topic'] = $responseTopic;
        }

        $this->publish($topic, $payload, $properties);
    }

    /**
     * Publish with user properties (MQTT v5)
     *
     * @param string $topic MQTT topic
     * @param array|string $payload Message payload
     * @param array $userProperties Key-value pairs of user properties
     */
    public function publishWithUserProperties(
        string $topic,
        array|string $payload,
        array $userProperties
    ): void {
        $properties = [
            'user_properties' => $userProperties,
        ];

        $this->publish($topic, $payload, $properties);
    }

    /**
     * Get or create MQTT protocol instance
     */
    private function getMqttProtocol(): MqttProtocol
    {
        if ($this->mqttProtocol === null) {
            $secrets = $this->secretQueryService->getSecretsByCategory('mqtt');

            $this->mqttProtocol = new MqttProtocol(
                host: $secrets['mqtt.host'] ?? throw new RuntimeException('MQTT host not configured'),
                port: (int) ($secrets['mqtt.port'] ?? 1883),
                username: $secrets['mqtt.username'] ?? null,
                password: $secrets['mqtt.password'] ?? null,
                protocolLevel: (int) ($secrets['mqtt.protocol_level'] ?? 5),
                qos: (int) ($secrets['mqtt.qos'] ?? 1),
                retain: (bool) ($secrets['mqtt.retain'] ?? false),
            );

            $this->mqttProtocol->connect();
        }

        return $this->mqttProtocol;
    }

    public function __destruct()
    {
        if ($this->mqttProtocol !== null) {
            $this->mqttProtocol->disconnect();
        }
    }
}
