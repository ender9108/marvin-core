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

namespace Marvin\Protocol\Domain\Model;

use Marvin\Protocol\Domain\ValueObject\ExecutionMode;
use Marvin\Shared\Domain\ValueObject\Identity\CorrelationId;

/**
 * Interface for protocol adapters that transform device-specific messages
 * into domain events and send commands to devices.
 */
interface ProtocolAdapterInterface
{
    /**
     * Get the adapter name (e.g., 'zigbee2mqtt', 'tasmota', 'shelly_gen2')
     */
    public function getName(): string;

    /**
     * Get the supported protocol types for this adapter
     *
     * @return string[] Array of protocol types (e.g., ['zigbee', 'network'])
     */
    public function getSupportedProtocols(): array;

    /**
     * Check if this adapter can handle a given device
     *
     * @param string $protocol The protocol type (e.g., 'zigbee', 'network')
     * @param array $deviceMetadata Device metadata (manufacturer, model, etc.)
     */
    public function supports(string $protocol, array $deviceMetadata = []): bool;

    /**
     * Send a command to a device
     *
     * @param string $nativeId Device native identifier (e.g., friendly_name for Zigbee, topic for Tasmota, IP for REST)
     * @param string $action Action to perform (e.g., 'turn_on', 'set_brightness')
     * @param array $parameters Action parameters
     * @param ExecutionMode $mode Execution mode (CORRELATION_ID, DEVICE_LOCK, FIRE_AND_FORGET)
     * @param CorrelationId|null $correlationId Correlation ID for tracking (if applicable)
     * @return array|null Result of the command (null if FIRE_AND_FORGET)
     */
    public function sendCommand(
        string $nativeId,
        string $action,
        array $parameters = [],
        ExecutionMode $mode = ExecutionMode::DEVICE_LOCK,
        ?CorrelationId $correlationId = null
    ): ?array;

    /**
     * Transform incoming protocol message to domain event data
     *
     * @param string $topic MQTT topic or message identifier
     * @param array $payload Message payload
     * @return array|null Domain event data or null if not applicable
     */
    public function transformMessage(string $topic, array $payload): ?array;

    /**
     * Get default execution mode for this adapter
     */
    public function getDefaultExecutionMode(): ExecutionMode;

    /**
     * Check if this adapter supports correlation-based synchronization
     */
    public function supportsCorrelation(): bool;
}
