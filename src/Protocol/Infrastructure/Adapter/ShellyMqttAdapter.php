<?php

declare(strict_types=1);

namespace Marvin\Protocol\Infrastructure\Adapter;

use Marvin\Protocol\Domain\Model\ProtocolAdapterInterface;
use Marvin\Protocol\Domain\ValueObject\ExecutionMode;
use Marvin\Protocol\Infrastructure\Service\MqttPublisher;
use Marvin\Shared\Domain\ValueObject\Identity\CorrelationId;

/**
 * Adapter for Shelly devices via MQTT
 * Handles both Gen1 and Gen2/3 Shelly devices configured to use MQTT
 */
final readonly class ShellyMqttAdapter implements ProtocolAdapterInterface
{
    public function __construct(
        private MqttPublisher $mqttPublisher,
    ) {
    }

    public function getName(): string
    {
        return 'shelly_mqtt';
    }

    public function getProtocolType(): string
    {
        return 'mqtt';
    }

    public function getSupportedProtocols(): array
    {
        return ['mqtt'];
    }

    public function supports(string $protocol, array $deviceMetadata = []): bool
    {
        $manufacturer = strtolower($deviceMetadata['manufacturer'] ?? '');
        $adapter = strtolower($deviceMetadata['adapter'] ?? '');

        return $protocol === 'mqtt'
            && ($manufacturer === 'shelly' || $adapter === 'shelly');
    }

    public function sendCommand(
        string $nativeId,
        string $action,
        array $parameters = [],
        ExecutionMode $mode = ExecutionMode::DEVICE_LOCK,
        ?CorrelationId $correlationId = null
    ): ?array {
        // nativeId is the Shelly device ID used in MQTT topics (e.g., "shellyplus1-ABC123")
        // Already resolved by DeviceQueryService in SendDeviceCommandHandler or ProtocolCapabilityService
        $deviceTopic = $nativeId;

        // Build MQTT topic and payload
        [$topic, $payload] = $this->buildCommand($deviceTopic, $action, $parameters);

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
        // Shelly MQTT topics: shellies/{device_id}/relay/{relay_id}
        if (!preg_match('#^shellies/([^/]+)/relay/(\d+)$#', $topic, $matches)) {
            return null;
        }

        $deviceId = $matches[1];
        $relayId = (int) $matches[2];

        $capabilities = [];

        // Extract relay state
        if (isset($payload['state'])) {
            $state = strtoupper($payload['state']);
            $capabilities[] = [
                'name' => 'switch',
                'value' => $state === 'ON',
                'metadata' => ['relay' => $relayId],
            ];
        }

        // Extract power measurement
        if (isset($payload['power'])) {
            $capabilities[] = [
                'name' => 'power',
                'value' => $payload['power'],
                'metadata' => ['relay' => $relayId],
            ];
        }

        if (empty($capabilities)) {
            return null;
        }

        return [
            'nativeId' => $deviceId,
            'capabilities' => $capabilities,
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
        return 'Shelly MQTT adapter for Shelly devices configured to use MQTT';
    }

    public function supportsCorrelation(): bool
    {
        return true;
    }

    /**
     * Build Shelly MQTT command
     *
     * Shelly MQTT Topics Reference:
     * @see https://shelly-api-docs.shelly.cloud/gen1/#mqtt
     *
     * Common topics:
     * - shellies/{device_id}/relay/{id}/command - Relay control (on, off, toggle)
     * - shellies/{device_id}/light/{id}/command - Light control (on, off, toggle)
     * - shellies/{device_id}/light/{id}/set - Light parameters (brightness, rgb, etc.)
     * - shellies/{device_id}/color/{id}/set - RGBW color control
     * - shellies/{device_id}/roller/{id}/command - Roller shutter (open, close, stop)
     * - shellies/{device_id}/roller/{id}/command/pos - Shutter position
     *
     * @return array{string, string} [topic, payload]
     */
    private function buildCommand(string $deviceTopic, string $action, array $parameters): array
    {
        $relay = (int) ($parameters['relay'] ?? 0);
        $light = (int) ($parameters['light'] ?? 0);
        $roller = (int) ($parameters['roller'] ?? 0);

        return match ($action) {
            // ========================================
            // BASIC SWITCH & RELAY
            // ========================================
            'turn_on' => [
                "shellies/$deviceTopic/relay/$relay/command",
                'on',
            ],
            'turn_off' => [
                "shellies/$deviceTopic/relay/$relay/command",
                'off',
            ],
            'toggle' => [
                "shellies/$deviceTopic/relay/$relay/command",
                'toggle',
            ],

            // ========================================
            // BRIGHTNESS (Lights/Dimmers)
            // ========================================
            'set_brightness' => [
                "shellies/$deviceTopic/light/$light/set",
                json_encode([
                    'turn' => 'on',
                    'brightness' => min(100, max(0, $parameters['brightness'] ?? 100)),
                ]),
            ],

            'increase_brightness' => [
                "shellies/$deviceTopic/light/$light/set",
                json_encode([
                    'brightness' => min(100, (int) ($parameters['current'] ?? 50) + (int) ($parameters['step'] ?? 10)),
                ]),
            ],

            'decrease_brightness' => [
                "shellies/$deviceTopic/light/$light/set",
                json_encode([
                    'brightness' => max(0, (int) ($parameters['current'] ?? 50) - (int) ($parameters['step'] ?? 10)),
                ]),
            ],

            // ========================================
            // COLOR - RGB/RGBW
            // ========================================
            'set_color', 'set_color_rgb' => [
                "shellies/$deviceTopic/color/$light/set",
                json_encode([
                    'turn' => 'on',
                    'red' => min(255, max(0, $parameters['r'] ?? 255)),
                    'green' => min(255, max(0, $parameters['g'] ?? 255)),
                    'blue' => min(255, max(0, $parameters['b'] ?? 255)),
                    'gain' => min(100, max(0, $parameters['gain'] ?? 100)),
                ]),
            ],

            'set_color_hex' => [
                "shellies/$deviceTopic/color/$light/set",
                json_encode([
                    'turn' => 'on',
                    'red' => hexdec(substr((string) str_replace('#', '', $parameters['hex'] ?? 'FFFFFF'), 0, 2)),
                    'green' => hexdec(substr((string) str_replace('#', '', $parameters['hex'] ?? 'FFFFFF'), 2, 2)),
                    'blue' => hexdec(substr((string) str_replace('#', '', $parameters['hex'] ?? 'FFFFFF'), 4, 2)),
                ]),
            ],

            // ========================================
            // COLOR TEMPERATURE
            // ========================================
            'set_color_temp', 'set_color_temperature' => [
                "shellies/$deviceTopic/light/$light/set",
                json_encode([
                    'turn' => 'on',
                    'temp' => min(6500, max(3000, $parameters['temp'] ?? 4000)),
                ]),
            ],

            'set_warm_white' => [
                "shellies/$deviceTopic/light/$light/set",
                json_encode(['turn' => 'on', 'temp' => 3000]),
            ],

            'set_neutral_white' => [
                "shellies/$deviceTopic/light/$light/set",
                json_encode(['turn' => 'on', 'temp' => 4000]),
            ],

            'set_cool_white' => [
                "shellies/$deviceTopic/light/$light/set",
                json_encode(['turn' => 'on', 'temp' => 6500]),
            ],

            // ========================================
            // LIGHT EFFECTS
            // ========================================
            'set_effect' => [
                "shellies/$deviceTopic/color/$light/set",
                json_encode([
                    'effect' => min(3, max(0, $parameters['effect'] ?? 0)),
                ]),
            ],

            'stop_effect' => [
                "shellies/$deviceTopic/color/$light/set",
                json_encode(['effect' => 0]),
            ],

            // ========================================
            // ROLLER SHUTTERS
            // ========================================
            'open' => [
                "shellies/$deviceTopic/roller/$roller/command",
                'open',
            ],

            'close' => [
                "shellies/$deviceTopic/roller/$roller/command",
                'close',
            ],

            'stop' => [
                "shellies/$deviceTopic/roller/$roller/command",
                'stop',
            ],

            'set_position', 'open_to_position', 'close_to_position' => [
                "shellies/$deviceTopic/roller/$roller/command/pos",
                (string) min(100, max(0, $parameters['position'] ?? 50)),
            ],

            // ========================================
            // ENERGY MONITORING
            // ========================================
            'reset_energy' => [
                "shellies/$deviceTopic/meter/$relay/command",
                'reset',
            ],

            // ========================================
            // SYSTEM
            // ========================================
            'identify' => [
                "shellies/$deviceTopic/relay/$relay/command",
                'toggle',
            ],

            'configure' => [
                "shellies/$deviceTopic/command",
                'status',
            ],

            'reset_configuration' => [
                "shellies/$deviceTopic/command",
                'reboot',
            ],

            // ========================================
            // DEFAULT - Pass-through
            // ========================================
            default => [
                "shellies/$deviceTopic/command",
                json_encode($parameters),
            ],
        };
    }
}
