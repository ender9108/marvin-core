<?php

namespace Marvin\System\Domain\Service;

use Simps\MQTT\Client;

interface MqttClientInterface
{
    public function connect(bool $cleanSession = true, array $will = []): void;

    public function subscribe(array $topics): void;

    public function publish(string $topic, string $payload, int $qos = 0, bool $retain = false): mixed;

    public function loop(callable $onMessage): void;

    public function stop(): void;

    public function disconnect(): void;

    public function getClient(): Client;
}
