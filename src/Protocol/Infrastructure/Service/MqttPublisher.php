<?php

declare(strict_types=1);

namespace Marvin\Protocol\Infrastructure\Service;

use Marvin\Protocol\Application\Service\Acl\SecretQueryServiceInterface;
use Marvin\Protocol\Infrastructure\Protocol\MqttProtocol;

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
            $credentials = $this->secretQueryService->getMqttCredentials();

            $this->mqttProtocol = new MqttProtocol(
                host: $credentials->host,
                port: $credentials->port,
                username: $credentials->username,
                password: $credentials->password,
                protocolLevel: $credentials->protocolLevel,
                qos: $credentials->qos,
                retain: $credentials->retain,
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
