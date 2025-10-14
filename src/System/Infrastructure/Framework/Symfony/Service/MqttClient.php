<?php

namespace Marvin\System\Infrastructure\Framework\Symfony\Service;

use AllowDynamicProperties;
use Marvin\System\Domain\Exception\MqttConnectionError;
use Marvin\System\Domain\Exception\MqttLoopError;
use Marvin\System\Domain\Exception\MqttPublishError;
use Marvin\System\Domain\Exception\MqttSubscribeError;
use Marvin\System\Domain\Service\MqttClientInterface;
use Psr\Log\LoggerInterface;
use Simps\MQTT\Client;
use Simps\MQTT\Config\ClientConfig;
use Simps\MQTT\Protocol\Types;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Throwable;

class MqttClient implements MqttClientInterface
{
    private Client $client;
    private int $lastPingAt;
    private bool $running = false;

    private string $clientId;

    public function __construct(
        #[Autowire(env: 'MQTT_HOST')]
        private readonly string $host,
        #[Autowire(env: 'MQTT_PORT')]
        private readonly int $port,
        #[Autowire(env: 'MQTT_USER')]
        private readonly string $user,
        #[Autowire(env: 'MQTT_PASSWORD')]
        private readonly string $password,
        #[Autowire(env: 'MQTT_PROTOCOL_LEVEL')]
        private readonly int $protocolLevel,
        #[Autowire(env: 'MQTT_TLS')]
        private readonly bool $useTls,
        #[Autowire(env: 'MQTT_CA_FILE')]
        private readonly string $caPath,
        #[Autowire(env: 'MQTT_SSL_CERT_FILE')]
        private readonly string $sslCertFile,
        #[Autowire(env: 'MQTT_SSL_KEY_FILE')]
        private readonly string $sslKeyFile,
        #[Autowire(env: 'MQTT_SSL_VERIFY_PEER')]
        private readonly bool $verifyPeer,
        #[Autowire(env: 'MQTT_SSL_ALLOW_SELF_SIGNED')]
        private readonly bool $sslAllowSelfSigned,
        private readonly LoggerInterface $logger,
    ) {
        $config = $this->buildConfig();
        $this->client = new Client($this->host, $this->port, $config);
        $this->lastPingAt = time();
    }

    public function connect(bool $cleanSession = true, array $will = []): void
    {
        try {
            $this->client->connect($cleanSession, $will);
            $this->logger->info('MQTT connected', [
                'host' => $this->host,
                'port' => $this->port,
                'client_id' => $this->clientId,
            ]);
            $this->lastPingAt = time();
        } catch (Throwable $e) {
            $this->logger->error('MQTT connect error: ' . $e->getMessage(), ['exception' => $e]);
            throw MqttConnectionError::withError($e->getMessage());
        }
    }

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

    public function stop(): void
    {
        $this->running = false;
    }

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

    private function buildConfig(): ClientConfig
    {
        $this->clientId = Client::genClientID();

        $config = new ClientConfig();
        $config->setClientId($this->clientId);
        $config->setUsername($this->user);
        $config->setPassword($this->password);
        $config->setKeepAlive(10);
        $config->setDelay(3000);
        $config->setMaxAttempts(5);
        $config->setProperties([
            'session_expiry_interval' => 60,
            'receive_maximum' => 65535,
            'topic_alias_maximum' => 65535,
        ]);
        $config->setProtocolLevel($this->protocolLevel);

        $tlsConfig = [];

        if ($this->useTls) {
            $tlsConfig = [
                'ssl_allow_self_signed' => $this->sslAllowSelfSigned,
                'ssl_verify_peer' => $this->verifyPeer,
                'ssl_cafile' => $this->caPath,
                'ssl_key_file' => $this->sslKeyFile,
                'ssl_cert_file' => $this->sslCertFile,
            ];
        }

        $swoolMqttConfig = array_merge(SWOOLE_MQTT_CONFIG, $tlsConfig);

        $config->setSwooleConfig($swoolMqttConfig);

        return $config;
    }
}
