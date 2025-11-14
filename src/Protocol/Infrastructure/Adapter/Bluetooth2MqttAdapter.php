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

namespace Marvin\Protocol\Infrastructure\Adapter;

use Marvin\Protocol\Domain\Model\ProtocolAdapterInterface;
use Marvin\Protocol\Domain\ValueObject\ExecutionMode;
use Marvin\Protocol\Infrastructure\Service\MqttPublisher;
use Marvin\Shared\Domain\ValueObject\Identity\CorrelationId;

/**
 * Adapter for Bluetooth devices via ESP32 Bluetooth Proxy
 * Handles BLE devices through ESP32 MQTT bridge
 */
final readonly class Bluetooth2MqttAdapter implements ProtocolAdapterInterface
{
    public function __construct(
        private MqttPublisher $mqttPublisher,
    ) {
    }

    public function getName(): string
    {
        return 'bluetooth2mqtt';
    }

    public function getProtocolType(): string
    {
        return 'mqtt';
    }

    public function getSupportedProtocols(): array
    {
        return ['bluetooth', 'ble', 'mqtt'];
    }

    public function supports(string $protocol, array $deviceMetadata = []): bool
    {
        $adapter = strtolower($deviceMetadata['adapter'] ?? '');

        return in_array($protocol, ['bluetooth', 'ble', 'mqtt'], true)
            && ($adapter === 'bluetooth2mqtt' || $adapter === 'ble');
    }

    public function sendCommand(
        string $nativeId,
        string $action,
        array $parameters = [],
        ExecutionMode $mode = ExecutionMode::DEVICE_LOCK,
        ?CorrelationId $correlationId = null
    ): ?array {
        // nativeId is the BLE device MAC address (e.g., "AA:BB:CC:DD:EE:FF")
        // Already resolved by DeviceQueryService in SendDeviceCommandHandler or ProtocolCapabilityService
        $macAddress = $nativeId;

        // Build MQTT topic for BLE device
        $topic = sprintf('marvin/bluetooth/%s/set', $macAddress);

        // Build command payload
        $payload = $this->buildCommandPayload($action, $parameters);

        // Send command
        if ($mode === ExecutionMode::CORRELATION_ID && $correlationId !== null) {
            $this->mqttPublisher->publishWithCorrelation(
                $topic,
                $payload,
                $correlationId->toString(),
                'marvin/response/' . $correlationId->toString()
            );
        } else {
            $this->mqttPublisher->publish($topic, $payload);
        }

        if ($mode === ExecutionMode::FIRE_AND_FORGET) {
            return null;
        }

        return ['status' => 'pending'];
    }

    public function transformMessage(string $topic, array $payload): ?array
    {
        // BLE topics: marvin/bluetooth/{mac_address}/{metric}
        if (!preg_match('#^marvin/bluetooth/([A-F0-9:]+)(?:/(.+))?$#i', $topic, $matches)) {
            return null;
        }

        $macAddress = $matches[1];
        $metric = $matches[2] ?? null;

        $capabilities = [];

        // Handle different message formats
        if ($metric !== null) {
            // Single metric message (e.g., marvin/bluetooth/AA:BB:CC:DD:EE:FF/temperature)
            $capabilities[] = [
                'name' => $metric,
                'value' => is_array($payload) ? ($payload['value'] ?? $payload) : $payload,
            ];
        } else {
            // Multi-metric message
            foreach ($payload as $key => $value) {
                // Skip metadata fields
                if (in_array($key, ['rssi', 'battery', 'last_seen'], true)) {
                    continue;
                }

                $capabilities[] = [
                    'name' => $key,
                    'value' => $value,
                ];
            }
        }

        if (empty($capabilities)) {
            return null;
        }

        return [
            'nativeId' => $macAddress,
            'capabilities' => $capabilities,
            'metadata' => [
                'rssi' => $payload['rssi'] ?? null,
                'battery' => $payload['battery'] ?? null,
                'last_seen' => $payload['last_seen'] ?? null,
            ],
        ];
    }

    public function getDefaultExecutionMode(): ExecutionMode
    {
        return ExecutionMode::CORRELATION_ID;
    }

    public function getSupportedExecutionModes(): array
    {
        return [
            ExecutionMode::CORRELATION_ID->value,
            ExecutionMode::DEVICE_LOCK->value,
            ExecutionMode::FIRE_AND_FORGET->value,
        ];
    }

    public function getDescription(): string
    {
        return 'Bluetooth2MQTT adapter for BLE devices via ESP32 Bluetooth Proxy';
    }

    public function supportsCorrelation(): bool
    {
        return true;
    }

    /**
     * Build command payload for BLE device
     *
     * Bluetooth Low Energy Command Reference:
     * BLE devices communicate via GATT characteristics (UUID-based)
     *
     * Common BLE device types:
     * - Smart Locks: lock/unlock commands
     * - BLE Lights: on/off, brightness, color (simplified RGB)
     * - Sensors: read characteristic values
     * - Generic: read/write GATT characteristics
     *
     * Note: BLE capabilities are more limited than Zigbee/WiFi protocols
     *
     * @return array<string, mixed>
     */
    private function buildCommandPayload(string $action, array $parameters): array
    {
        return match ($action) {
            // ========================================
            // BASIC SWITCH & LIGHT
            // ========================================
            'turn_on' => [
                'action' => 'write',
                'characteristic' => $parameters['characteristic'] ?? 'power',
                'value' => 'on',
            ],

            'turn_off' => [
                'action' => 'write',
                'characteristic' => $parameters['characteristic'] ?? 'power',
                'value' => 'off',
            ],

            'toggle' => [
                'action' => 'toggle',
                'characteristic' => $parameters['characteristic'] ?? 'power',
            ],

            // ========================================
            // BRIGHTNESS
            // ========================================
            'set_brightness' => [
                'action' => 'write',
                'characteristic' => $parameters['characteristic'] ?? 'brightness',
                'value' => min(100, max(0, $parameters['brightness'] ?? 100)),
            ],

            'increase_brightness' => [
                'action' => 'write',
                'characteristic' => 'brightness',
                'value' => min(100, (int) ($parameters['current'] ?? 50) + (int) ($parameters['step'] ?? 10)),
            ],

            'decrease_brightness' => [
                'action' => 'write',
                'characteristic' => 'brightness',
                'value' => max(0, (int) ($parameters['current'] ?? 50) - (int) ($parameters['step'] ?? 10)),
            ],

            // ========================================
            // COLOR - Simplified for BLE
            // ========================================
            'set_color', 'set_color_rgb' => [
                'action' => 'write',
                'characteristic' => 'color',
                'value' => sprintf(
                    '%02X%02X%02X',
                    (int) min(255, max(0, $parameters['r'] ?? 255)),
                    (int) min(255, max(0, $parameters['g'] ?? 255)),
                    (int) min(255, max(0, $parameters['b'] ?? 255))
                ),
            ],

            'set_color_hex' => [
                'action' => 'write',
                'characteristic' => 'color',
                'value' => (string) str_replace('#', '', $parameters['hex'] ?? 'FFFFFF'),
            ],

            // ========================================
            // SECURITY - LOCKS
            // ========================================
            'lock' => [
                'action' => 'lock',
                'characteristic' => $parameters['characteristic'] ?? 'lock_control',
            ],

            'unlock' => [
                'action' => 'unlock',
                'characteristic' => $parameters['characteristic'] ?? 'lock_control',
            ],

            'unlock_with_timeout' => [
                'action' => 'unlock',
                'characteristic' => 'lock_control',
                'timeout' => $parameters['timeout'] ?? 30,
            ],

            // ========================================
            // NOTIFICATION
            // ========================================
            'send_notification' => [
                'action' => 'notify',
                'message' => $parameters['message'] ?? 'Notification',
            ],

            'play_sound' => [
                'action' => 'write',
                'characteristic' => 'sound',
                'value' => $parameters['sound'] ?? 'beep',
            ],

            'flash_light' => [
                'action' => 'blink',
                'characteristic' => 'led',
            ],

            // ========================================
            // GENERIC BLE OPERATIONS
            // ========================================
            'read' => [
                'action' => 'read',
                'characteristic' => $parameters['characteristic'] ?? null,
            ],

            'write' => [
                'action' => 'write',
                'characteristic' => $parameters['characteristic'] ?? null,
                'value' => $parameters['value'] ?? null,
            ],

            'notify' => [
                'action' => 'notify',
                'characteristic' => $parameters['characteristic'] ?? null,
                'enable' => $parameters['enable'] ?? true,
            ],

            'indicate' => [
                'action' => 'indicate',
                'characteristic' => $parameters['characteristic'] ?? null,
                'enable' => $parameters['enable'] ?? true,
            ],

            // ========================================
            // SYSTEM
            // ========================================
            'identify' => [
                'action' => 'blink',
                'characteristic' => 'led',
            ],

            'configure' => [
                'action' => 'read',
                'characteristic' => 'device_info',
            ],

            // ========================================
            // DEFAULT - Pass-through with action
            // ========================================
            default => array_merge(['action' => $action], $parameters),
        };
    }
}
