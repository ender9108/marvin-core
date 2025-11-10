<?php

declare(strict_types=1);

namespace Marvin\Protocol\Infrastructure\Service\Acl;

use Marvin\Protocol\Application\Service\Acl\Dto\MqttCredentialsDto;
use Marvin\Protocol\Application\Service\Acl\Dto\RestCredentialsDto;
use Marvin\Protocol\Application\Service\Acl\Dto\WebSocketCredentialsDto;
use Marvin\Protocol\Application\Service\Acl\SecretQueryServiceInterface;
use Marvin\Secret\Domain\Repository\SecretRepositoryInterface;
use Marvin\Secret\Domain\Service\EncryptionServiceInterface;
use Marvin\Secret\Domain\ValueObject\SecretKey;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Throwable;

/**
 * ACL Service for querying Secret Context from Protocol Context
 *
 * Retrieves encrypted secrets from Secret Context and provides them
 * in decrypted form for Protocol implementations
 */
final readonly class SecretQueryService implements SecretQueryServiceInterface
{
    public function __construct(
        private SecretRepositoryInterface $secretRepository,
        private EncryptionServiceInterface $encryptionService,
        private LoggerInterface $logger,
    ) {
    }

    public function getSecret(string $key): string
    {
        try {
            $secret = $this->secretRepository->byKey(new SecretKey($key));

            if ($secret === null) {
                $this->logger->warning('Secret not found, using environment variable fallback', [
                    'key' => $key,
                ]);

                // Fallback to environment variable
                return $_ENV[$key] ?? throw new RuntimeException(
                    sprintf('Secret not found: %s (not in Secret Context nor in environment)', $key)
                );
            }

            // Decrypt and return the secret value
            return $secret->value->decrypt($this->encryptionService);
        } catch (Throwable $e) {
            $this->logger->error('Error retrieving secret from Secret Context', [
                'key' => $key,
                'error' => $e->getMessage(),
            ]);

            throw new RuntimeException(sprintf('Failed to retrieve secret: %s', $key), 0, $e);
        }
    }

    public function getProtocolCredentials(string $protocolId): array
    {
        try {
            // Try to get credentials stored as JSON in Secret Context
            $credentialsJson = $this->getSecret(sprintf('protocol_%s_credentials', $protocolId));

            $credentials = json_decode($credentialsJson, true, 512, JSON_THROW_ON_ERROR);

            if (!is_array($credentials)) {
                throw new RuntimeException('Invalid credentials format: expected JSON array');
            }

            return $credentials;
        } catch (Throwable $e) {
            $this->logger->error('Error retrieving protocol credentials', [
                'protocolId' => $protocolId,
                'error' => $e->getMessage(),
            ]);

            throw new RuntimeException(sprintf('Failed to retrieve credentials for protocol: %s', $protocolId), 0, $e);
        }
    }

    public function getMqttCredentials(): MqttCredentialsDto
    {
        try {
            // Try to get from Secret Context first
            $host = $this->getSecretWithFallback('MQTT_HOST', 'mosquitto');
            $port = (int) $this->getSecretWithFallback('MQTT_PORT', '1883');
            $username = $this->getSecretWithFallback('MQTT_USERNAME', null);
            $password = $this->getSecretWithFallback('MQTT_PASSWORD', null);

            return new MqttCredentialsDto(
                host: $host,
                port: $port,
                username: $username,
                password: $password,
                protocolLevel: 5, // MQTT v5
                qos: 1,
                retain: false,
            );
        } catch (Throwable $e) {
            $this->logger->warning('Error retrieving MQTT credentials, using environment fallback', [
                'error' => $e->getMessage(),
            ]);

            // Fallback to environment variables
            return new MqttCredentialsDto(
                host: $_ENV['MQTT_HOST'] ?? 'mosquitto',
                port: (int) ($_ENV['MQTT_PORT'] ?? 1883),
                username: $_ENV['MQTT_USERNAME'] ?? null,
                password: $_ENV['MQTT_PASSWORD'] ?? null,
                protocolLevel: 5,
                qos: 1,
                retain: false,
            );
        }
    }

    public function getWebSocketCredentials(string $url): ?WebSocketCredentialsDto
    {
        try {
            // Extract host from URL for secret key
            $host = parse_url($url, PHP_URL_HOST) ?? 'unknown';
            $secretKey = sprintf('websocket_%s_credentials', str_replace('.', '_', $host));

            $credentialsJson = $this->getSecret($secretKey);
            $credentials = json_decode($credentialsJson, true, 512, JSON_THROW_ON_ERROR);

            return new WebSocketCredentialsDto(
                url: $url,
                ssl: $credentials['ssl'] ?? str_starts_with($url, 'wss://'),
                timeout: $credentials['timeout'] ?? 5.0,
                headers: $credentials['headers'] ?? [],
                authType: $credentials['auth_type'] ?? null,
                username: $credentials['username'] ?? null,
                password: $credentials['password'] ?? null,
            );
        } catch (Throwable $e) {
            $this->logger->debug('No WebSocket credentials found for URL', [
                'url' => $url,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    public function getRestCredentials(string $baseUri): ?RestCredentialsDto
    {
        try {
            // Extract host from URI for secret key
            $host = parse_url($baseUri, PHP_URL_HOST) ?? 'unknown';
            $secretKey = sprintf('rest_%s_credentials', str_replace('.', '_', $host));

            $credentialsJson = $this->getSecret($secretKey);
            $credentials = json_decode($credentialsJson, true, 512, JSON_THROW_ON_ERROR);

            return new RestCredentialsDto(
                baseUri: $baseUri,
                authType: $credentials['auth_type'] ?? null,
                username: $credentials['username'] ?? null,
                password: $credentials['password'] ?? null,
                bearerToken: $credentials['bearer_token'] ?? null,
                timeout: $credentials['timeout'] ?? 5.0,
                headers: $credentials['headers'] ?? [],
            );
        } catch (Throwable $e) {
            $this->logger->debug('No REST credentials found for URI', [
                'baseUri' => $baseUri,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Get secret with fallback to environment variable or default value
     */
    private function getSecretWithFallback(string $key, ?string $default = null): ?string
    {
        try {
            return $this->getSecret($key);
        } catch (Throwable) {
            return $_ENV[$key] ?? $default;
        }
    }
}
