<?php

namespace App\System\Infrastructure\Symfony\Service;

use Random\RandomException;
use Simps\MQTT\Client;
use Simps\MQTT\Config\ClientConfig;
use Simps\MQTT\Protocol\Types;
use Throwable;

final class MqttService
{
    private const array SWOOLE_MQTT_CONFIG = [
        'open_mqtt_protocol' => true,
        'package_max_length' => 2 * 1024 * 1024,
        'connect_timeout' => 5.0,
        'write_timeout' => 5.0,
        'read_timeout' => 5.0,
        'ssl' => false,
        'ssl_allow_self_signed' => false,
        'ssl_verify_peer' => true,
        'ssl_cafile' => null,
        'ssl_key_file' => null,
        'ssl_cert_file' => null,
    ];

    public const array MQTT_PROPERTIES = [
        'session_expiry_interval' => 60,
        'receive_maximum' => 65535,
        'topic_alias_maximum' => 65535,
    ];

    private Client $client;

    private bool $loop = false;

    private array $topics = [];

    /**
     * @throws RandomException
     */
    public function __construct(
        string $host,
        int $port,
        string $clientId,
        ?string $username = null,
        ?string $password = null,
        int $protocolLevel = 5,
        bool $useTls = false,
        ?string $certFile = null,
        ?string $keyFile = null,
        ?string $caFile = null,
        bool $verifyPeer = true,
        bool $allowSelfSigned = false,
    ) {
        $clientId = empty($clientId) ?? 'marvin-core-' . bin2hex(random_bytes(3));

        $config = new ClientConfig();
        $config->setUserName($username ?? '');
        $config->setPassword($password ?? '');
        $config->setClientId($clientId);
        $config->setProtocolLevel($protocolLevel);
        $config->setKeepAlive(60);
        $config->setProperties(self::MQTT_PROPERTIES);

        $swooleConfig = self::SWOOLE_MQTT_CONFIG;

        if ($useTls) {
            $swooleConfig['ssl'] = true;
            $swooleConfig['ssl_host_name'] = $host;
            $swooleConfig['ssl_cert_file'] = $certFile;
            $swooleConfig['ssl_key_file'] = $keyFile;
            $swooleConfig['ssl_ca_file'] = $caFile;
            $swooleConfig['ssl_verify_peer'] = $verifyPeer;
            $swooleConfig['ssl_allow_self_signed'] = $allowSelfSigned;
        }

        $config->setSwooleConfig($swooleConfig);

        $this->client = new Client($host, $port, $config);
    }

    public function connect(bool $clean = true, array $will = []): self
    {
        $this->client->connect($clean, $will);

        return $this;
    }

    public function disconnect(): void
    {
        $this->client->close();
    }

    public function publish(string $topic, string $message, int $qos = 1, bool $retain = false): self
    {
        $this->client->publish($topic, $message, $qos, $retain);

        return $this;
    }

    /**
     * @param callable(string $topic, string $message): void $callback
     */
    public function subscribe(string|array $topics, callable $callback, int $qos = 1): self
    {
        $topics = is_array($topics) ? $topics : [$topics];
        $subscribes = [];

        foreach ($topics as $topic) {
            $subscribes[$topic] = [
                'qos' => $qos,
                'no_local' => true,
                'retain_as_published' => true,
                'retain_handling' => 2,
            ];

            $this->topics[$topic] = $callback;
        }

        $this->client->subscribe($subscribes);

        return $this;
    }

    public function unSubscribe(string|array $topics, array $properties = []): self
    {
        $this->client->unsubscribe(
            is_array($topics) ? $topics : [$topics],
            $properties
        );

        return $this;
    }

    /**
     * @throws Throwable
     */
    public function startLoop(): void
    {
        $this->loop = true;
        $timeSincePing = time();

        while ($this->loop) {
            try {
                $buffer = $this->client->recv();

                if ($buffer && $buffer !== true) {
                    // QoS1 PUBACK
                    if ($buffer['type'] === Types::PUBLISH) {
                        if (isset( $this->topics[$buffer['topic']])) {
                            $this->topics[$buffer['topic']](
                                $buffer['topic'],
                                $buffer['message']
                            );
                        }

                        if ($buffer['qos'] === 1) {
                            $this->client->send(
                                ['type' => Types::PUBACK, 'message_id' => $buffer['message_id']],
                                false
                            );
                        }
                    }

                    if ($buffer['type'] === Types::DISCONNECT) {
                        $this->stopLoop();
                        break;
                    }
                }
                if ($timeSincePing <= (time() - $this->client->getConfig()->getKeepAlive())) {
                    $buffer = $this->client->ping();

                    if ($buffer) {
                        $timeSincePing = time();
                    }
                }
            } catch (Throwable $e) {
                $this->stopLoop(false);
                throw $e;
            }
        }
    }

    public function stopLoop(bool $disconnect = true): void
    {
        $this->loop = false;

        if (true === $disconnect) {
            $this->disconnect();
        }
    }

    public function __destruct()
    {
        $this->disconnect();
    }
}
