<?php

declare(strict_types=1);

namespace Marvin\Protocol\Application\Service\Acl;

use Marvin\Protocol\Application\Service\Acl\Dto\MqttCredentialsDto;
use Marvin\Protocol\Application\Service\Acl\Dto\RestCredentialsDto;
use Marvin\Protocol\Application\Service\Acl\Dto\WebSocketCredentialsDto;

/**
 * ACL Interface for querying Secret Context from Protocol Context
 */
interface SecretQueryServiceInterface
{
    /**
     * Récupère un secret par clé
     */
    public function getSecret(string $key): string;

    /**
     * Récupère les credentials d'un protocol
     */
    public function getProtocolCredentials(string $protocolId): array;

    /**
     * Récupère les credentials MQTT globaux
     */
    public function getMqttCredentials(): MqttCredentialsDto;

    /**
     * Récupère les credentials WebSocket pour une URL
     */
    public function getWebSocketCredentials(string $url): ?WebSocketCredentialsDto;

    /**
     * Récupère les credentials REST (Basic ou Digest)
     */
    public function getRestCredentials(string $baseUri): ?RestCredentialsDto;
}
