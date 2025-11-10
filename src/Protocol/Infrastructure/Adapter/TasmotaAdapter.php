<?php

declare(strict_types=1);

namespace Marvin\Protocol\Infrastructure\Adapter;

use Marvin\Protocol\Domain\Model\ProtocolAdapterInterface;
use Marvin\Protocol\Domain\ValueObject\ExecutionMode;
use Marvin\Protocol\Infrastructure\Service\MqttPublisher;
use Marvin\Shared\Domain\ValueObject\Identity\CorrelationId;

/**
 * Adapter for Tasmota devices
 * Handles ESP8266/ESP32 devices with Tasmota firmware via MQTT
 */
final readonly class TasmotaAdapter implements ProtocolAdapterInterface
{
    public function __construct(
        private MqttPublisher $mqttPublisher,
    ) {
    }

    public function getName(): string
    {
        return 'tasmota';
    }

    public function getProtocolType(): string
    {
        return 'mqtt';
    }

    public function getSupportedProtocols(): array
    {
        return ['mqtt', 'network'];
    }

    public function supports(string $protocol, array $deviceMetadata = []): bool
    {
        return $protocol === 'mqtt'
            && (($deviceMetadata['adapter'] ?? '') === 'tasmota'
                || ($deviceMetadata['manufacturer'] ?? '') === 'tasmota');
    }

    public function sendCommand(
        string $nativeId,
        string $action,
        array $parameters = [],
        ExecutionMode $mode = ExecutionMode::DEVICE_LOCK,
        ?CorrelationId $correlationId = null
    ): ?array {
        // nativeId is the Tasmota device topic (e.g., "tasmota_ABC123")
        // Already resolved by DeviceQueryService in SendDeviceCommandHandler or ProtocolCapabilityService
        $deviceTopic = $nativeId;

        // Build command topic and payload
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
        // Tasmota topics: stat/{device_id}/RESULT, tele/{device_id}/STATE
        if (!preg_match('#^(stat|tele)/([^/]+)/(RESULT|STATE)$#', $topic, $matches)) {
            return null;
        }

        $deviceId = $matches[2];
        $messageType = $matches[3];

        $capabilities = [];

        // Transform based on message type
        if ($messageType === 'RESULT') {
            // Extract power state
            if (isset($payload['POWER'])) {
                $capabilities[] = [
                    'name' => 'switch',
                    'value' => $payload['POWER'] === 'ON',
                ];
            }
        } elseif ($messageType === 'STATE') {
            // Extract various states
            if (isset($payload['POWER'])) {
                $capabilities[] = [
                    'name' => 'switch',
                    'value' => $payload['POWER'] === 'ON',
                ];
            }

            if (isset($payload['Dimmer'])) {
                $capabilities[] = [
                    'name' => 'brightness',
                    'value' => $payload['Dimmer'],
                ];
            }
        }

        if (empty($capabilities)) {
            return null;
        }

        return [
            'nativeId' => $deviceId,
            'capabilities' => $capabilities,
            'metadata' => [
                'rssi' => $payload['Wifi']['RSSI'] ?? null,
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
        return 'Tasmota adapter for ESP8266/ESP32 devices with Tasmota firmware';
    }

    public function supportsCorrelation(): bool
    {
        return true;
    }

    /**
     * Build Tasmota MQTT command
     *
     * Tasmota Command Reference:
     * @see https://tasmota.github.io/docs/Commands/
     *
     * Common commands:
     * - Power: POWER, POWER1, POWER2, etc. - Values: ON, OFF, TOGGLE
     * - Dimmer: 0-100 (brightness percentage)
     * - Color: RRGGBB (hex) or RRGGBBWWCC for RGBWW
     * - CT: 153-500 (color temperature in mired)
     * - Scheme: 0-4 (light effects)
     * - ShutterOpen, ShutterClose, ShutterStop, ShutterPosition: 0-100
     * - FanSpeed: 0-3
     *
     * @return array{string, string} [topic, payload]
     */
    private function buildCommand(string $deviceTopic, string $action, array $parameters): array
    {
        return match ($action) {
            // ========================================
            // BASIC SWITCH & LIGHT
            // ========================================
            'turn_on' => [
                "cmnd/$deviceTopic/POWER",
                'ON',
            ],
            'turn_off' => [
                "cmnd/$deviceTopic/POWER",
                'OFF',
            ],
            'toggle' => [
                "cmnd/$deviceTopic/POWER",
                'TOGGLE',
            ],

            // ========================================
            // BRIGHTNESS
            // ========================================
            'set_brightness' => [
                "cmnd/$deviceTopic/Dimmer",
                (string) min(100, max(0, $parameters['brightness'] ?? 100)),
            ],
            'increase_brightness' => [
                "cmnd/$deviceTopic/Dimmer",
                '+',
            ],
            'decrease_brightness' => [
                "cmnd/$deviceTopic/Dimmer",
                '-',
            ],

            // ========================================
            // COLOR - RGB/RGBW
            // ========================================
            'set_color', 'set_color_rgb' => [
                "cmnd/$deviceTopic/Color",
                sprintf(
                    '%02X%02X%02X',
                    min(255, max(0, $parameters['r'] ?? 255)),
                    min(255, max(0, $parameters['g'] ?? 255)),
                    min(255, max(0, $parameters['b'] ?? 255))
                ),
            ],

            'set_color_hex' => [
                "cmnd/$deviceTopic/Color",
                str_replace('#', '', $parameters['hex'] ?? 'FFFFFF'),
            ],

            'set_color_hsv' => [
                "cmnd/$deviceTopic/HsbColor",
                sprintf(
                    '%d,%d,%d',
                    min(360, max(0, $parameters['h'] ?? 0)),
                    min(100, max(0, $parameters['s'] ?? 100)),
                    min(100, max(0, $parameters['v'] ?? 100))
                ),
            ],

            'set_hue' => [
                "cmnd/$deviceTopic/HsbColor1",
                (string) min(360, max(0, $parameters['hue'] ?? 0)),
            ],

            'set_saturation' => [
                "cmnd/$deviceTopic/HsbColor2",
                (string) min(100, max(0, $parameters['saturation'] ?? 100)),
            ],

            // ========================================
            // COLOR TEMPERATURE
            // ========================================
            'set_color_temp', 'set_color_temperature', 'set_color_temperature_mired' => [
                "cmnd/$deviceTopic/CT",
                (string) min(500, max(153, $parameters['color_temp'] ?? $parameters['mired'] ?? 326)),
            ],

            'set_warm_white' => [
                "cmnd/$deviceTopic/CT",
                '500', // Warm (2000K)
            ],

            'set_neutral_white' => [
                "cmnd/$deviceTopic/CT",
                '326', // Neutral (3000K)
            ],

            'set_cool_white' => [
                "cmnd/$deviceTopic/CT",
                '153', // Cool (6500K)
            ],

            // ========================================
            // LIGHT EFFECTS
            // ========================================
            'set_effect' => [
                "cmnd/$deviceTopic/Scheme",
                match ($parameters['effect'] ?? 'none') {
                    'none' => '0',
                    'wakeup' => '1',
                    'cycleup' => '2',
                    'cycledown' => '3',
                    'random' => '4',
                    default => '0',
                },
            ],

            'stop_effect' => [
                "cmnd/$deviceTopic/Scheme",
                '0',
            ],

            'blink' => [
                "cmnd/$deviceTopic/Power",
                'BLINK',
            ],

            // ========================================
            // WINDOW COVERINGS & SHUTTERS
            // ========================================
            'open' => [
                "cmnd/$deviceTopic/ShutterOpen",
                '',
            ],

            'close' => [
                "cmnd/$deviceTopic/ShutterClose",
                '',
            ],

            'stop' => [
                "cmnd/$deviceTopic/ShutterStop",
                '',
            ],

            'set_position', 'open_to_position', 'close_to_position' => [
                "cmnd/$deviceTopic/ShutterPosition",
                (string) min(100, max(0, $parameters['position'] ?? 50)),
            ],

            // ========================================
            // FAN CONTROL
            // ========================================
            'set_fan_mode', 'set_fan_speed' => [
                "cmnd/$deviceTopic/FanSpeed",
                match ($parameters['mode'] ?? $parameters['speed'] ?? 'auto') {
                    'off', '0' => '0',
                    'low', '1' => '1',
                    'medium', '2' => '2',
                    'high', '3' => '3',
                    'auto' => '0',
                    default => '2',
                },
            ],

            'set_fan_auto' => [
                "cmnd/$deviceTopic/FanSpeed",
                '0',
            ],

            'set_fan_on' => [
                "cmnd/$deviceTopic/FanSpeed",
                '2',
            ],

            'increase_fan_speed' => [
                "cmnd/$deviceTopic/FanSpeed",
                '+',
            ],

            'decrease_fan_speed' => [
                "cmnd/$deviceTopic/FanSpeed",
                '-',
            ],

            // ========================================
            // ENERGY MONITORING
            // ========================================
            'reset_energy' => [
                "cmnd/$deviceTopic/EnergyReset",
                '3', // Reset total energy
            ],

            // ========================================
            // SYSTEM & CONFIGURATION
            // ========================================
            'identify' => [
                "cmnd/$deviceTopic/Power",
                'BLINK',
            ],

            'configure' => [
                "cmnd/$deviceTopic/Status",
                '0', // Request all status
            ],

            'reset_configuration' => [
                "cmnd/$deviceTopic/Reset",
                '1', // Reset to default settings
            ],

            // ========================================
            // TIMERS
            // ========================================
            'start_timer' => [
                "cmnd/$deviceTopic/Timer1",
                json_encode([
                    'Enable' => 1,
                    'Time' => $parameters['time'] ?? '00:00',
                    'Action' => 1, // Turn ON
                ]),
            ],

            'stop_timer' => [
                "cmnd/$deviceTopic/Timer1",
                json_encode(['Enable' => 0]),
            ],

            'reset_timer' => [
                "cmnd/$deviceTopic/Timers",
                '0', // Disable all timers
            ],

            // ========================================
            // DEFAULT - Pass-through for unmapped actions
            // ========================================
            default => [
                "cmnd/$deviceTopic/" . strtoupper($action),
                json_encode($parameters),
            ],
        };
    }
}
