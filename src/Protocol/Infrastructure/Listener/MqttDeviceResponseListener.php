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

namespace Marvin\Protocol\Infrastructure\Listener;

use Marvin\Device\Application\Command\PendingAction\CompletePendingAction;
use Marvin\Device\Application\Command\PendingAction\FailPendingAction;
use Marvin\Protocol\Application\Service\Acl\SecretQueryServiceInterface;
use Marvin\Protocol\Infrastructure\Protocol\MqttProtocol;
use Marvin\Shared\Domain\ValueObject\Identity\CorrelationId;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Throwable;

/**
 * MQTT Device Response Listener
 * Listens to MQTT response topics to resolve PendingActions with correlation IDs
 * This enables synchronous command execution with CORRELATION_ID mode
 */
final class MqttDeviceResponseListener
{
    private ?MqttProtocol $mqttProtocol = null;

    public function __construct(
        private readonly SecretQueryServiceInterface $secretQueryService,
        private readonly MessageBusInterface $commandBus,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * Start listening to MQTT response messages
     *
     * @param int|null $timeout Timeout in seconds (null for infinite)
     */
    public function listen(?int $timeout = null): void
    {
        $mqtt = $this->getMqttProtocol();

        $this->logger->info('Starting MQTT Device Response Listener', [
            'timeout' => $timeout,
        ]);

        // Connect to MQTT broker
        $mqtt->connect();

        // Subscribe to response topics
        $mqtt->subscribe([
            'marvin/response/#',  // Marvin response topics with correlation IDs
        ]);

        // Receive and process response messages
        $mqtt->receive(function (string $topic, array|string $payload, array $properties): void {
            $this->handleResponse($topic, $payload, $properties);
        }, $timeout);
    }

    /**
     * Handle incoming MQTT response message
     */
    private function handleResponse(string $topic, array|string $payload, array $properties): void
    {
        $this->logger->debug('Received MQTT response', [
            'topic' => $topic,
            'payload' => $payload,
            'properties' => $properties,
        ]);

        // Extract correlation ID from topic or properties
        $correlationId = $this->extractCorrelationId($topic, $properties);

        if ($correlationId === null) {
            $this->logger->warning('No correlation ID found in response', [
                'topic' => $topic,
            ]);
            return;
        }

        $this->logger->info('Response received for correlation ID', [
            'correlation_id' => $correlationId,
            'topic' => $topic,
            'payload' => $payload,
        ]);

        // Parse payload
        $result = is_string($payload) ? json_decode($payload, true) : $payload;

        if ($result === null && is_string($payload)) {
            $result = ['raw' => $payload];
        }

        // Check if response indicates error
        $isError = $this->isErrorResponse($result);

        try {
            if ($isError) {
                $errorMessage = $this->extractErrorMessage($result);

                $this->logger->warning('Device response indicates error', [
                    'correlation_id' => $correlationId,
                    'error' => $errorMessage,
                ]);

                // Dispatch FailPendingAction command to Device Context
                $command = new FailPendingAction(
                    correlationId: new CorrelationId($correlationId),
                    errorMessage: $errorMessage,
                );
            } else {
                $this->logger->info('Device response successful', [
                    'correlation_id' => $correlationId,
                ]);

                // Dispatch CompletePendingAction command to Device Context
                $command = new CompletePendingAction(
                    correlationId: new CorrelationId($correlationId),
                    result: $result ?? [],
                );
            }

            $this->commandBus->dispatch($command);

            $this->logger->debug('PendingAction update command dispatched', [
                'correlation_id' => $correlationId,
                'command' => $command::class,
            ]);
        } catch (Throwable $e) {
            $this->logger->error('Error dispatching PendingAction update', [
                'correlation_id' => $correlationId,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Check if response indicates an error
     */
    private function isErrorResponse(mixed $result): bool
    {
        if (!is_array($result)) {
            return false;
        }

        // Common error indicators
        return isset($result['error'])
            || isset($result['ERROR'])
            || (isset($result['status']) && in_array($result['status'], ['error', 'failed', 'ERROR', 'FAILED'], true))
            || (isset($result['success']) && $result['success'] === false);
    }

    /**
     * Extract error message from response
     */
    private function extractErrorMessage(array $result): string
    {
        return $result['error']
            ?? $result['ERROR']
            ?? $result['message']
            ?? $result['error_message']
            ?? json_encode($result);
    }

    /**
     * Extract correlation ID from topic or MQTT v5 properties
     */
    private function extractCorrelationId(string $topic, array $properties): ?string
    {
        // Try to extract from topic: marvin/response/{correlation_id}
        if (preg_match('#^marvin/response/(.+)$#', $topic, $matches)) {
            return $matches[1];
        }

        return $properties['correlation_data'] ?? null;
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
