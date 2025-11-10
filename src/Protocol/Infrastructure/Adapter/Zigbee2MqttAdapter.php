<?php

declare(strict_types=1);

namespace Marvin\Protocol\Infrastructure\Adapter;

use InvalidArgumentException;
use Marvin\Protocol\Domain\Model\ProtocolAdapterInterface;
use Marvin\Protocol\Domain\ValueObject\ExecutionMode;
use Marvin\Protocol\Domain\ValueObject\TransportType;
use Marvin\Protocol\Infrastructure\Service\MqttPublisher;
use Marvin\Shared\Domain\ValueObject\Identity\CorrelationId;

/**
 * Adapter for Zigbee2MQTT devices
 * Handles Zigbee devices via MQTT bridge
 */
final readonly class Zigbee2MqttAdapter implements ProtocolAdapterInterface
{
    public function __construct(
        private MqttPublisher $mqttPublisher,
    ) {
    }

    public function getName(): string
    {
        return 'zigbee2mqtt';
    }

    public function getProtocolType(): string
    {
        return TransportType::MQTT->value;
    }

    public function getSupportedProtocols(): array
    {
        return ['zigbee', 'mqtt'];
    }

    public function supports(string $protocol, array $deviceMetadata = []): bool
    {
        // Supports Zigbee protocol or MQTT with zigbee2mqtt in metadata
        return
            $protocol === 'zigbee' ||
            ($protocol === 'mqtt' && ($deviceMetadata['adapter'] ?? '') === 'zigbee2mqtt')
        ;
    }

    public function sendCommand(
        string $nativeId,
        string $action,
        array $parameters = [],
        ExecutionMode $mode = ExecutionMode::DEVICE_LOCK,
        ?CorrelationId $correlationId = null
    ): ?array {
        // Handle bridge commands (group management, etc.)
        if (str_starts_with($action, 'bridge_')) {
            return $this->sendBridgeCommand($action, $parameters, $mode, $correlationId);
        }

        // nativeId is the Zigbee friendly name (e.g., "living_room_light")
        // Already resolved by DeviceQueryService in SendDeviceCommandHandler or ProtocolCapabilityService
        $friendlyName = $nativeId;

        // Build MQTT topic for command
        $topic = sprintf('zigbee2mqtt/%s/set', $friendlyName);

        // Build command payload based on action
        $payload = $this->buildCommandPayload($action, $parameters);

        // Send command with or without correlation
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

        // Return null for FIRE_AND_FORGET mode
        if ($mode === ExecutionMode::FIRE_AND_FORGET) {
            return null;
        }

        // For CORRELATION_ID and DEVICE_LOCK, result will be handled by listeners
        // Note: Polling mechanism is implemented in ProtocolCapabilityService (Device Context)
        // which waits for PendingAction completion via MqttDeviceResponseListener
        return ['status' => 'pending'];
    }

    public function transformMessage(string $topic, array $payload): ?array
    {
        // Extract friendly name from topic: zigbee2mqtt/{friendly_name}
        if (!preg_match('#^zigbee2mqtt/([^/]+)$#', $topic, $matches)) {
            return null;
        }

        $friendlyName = $matches[1];

        // Skip bridge messages
        if ($friendlyName === 'bridge') {
            return null;
        }

        // Transform payload to device state data
        $capabilities = [];

        // Map common Zigbee capabilities
        foreach ($payload as $key => $value) {
            // Skip metadata fields
            if (in_array($key, ['linkquality', 'last_seen'], true)) {
                continue;
            }

            $capabilities[] = [
                'name' => $key,
                'value' => $value,
            ];
        }

        return [
            'nativeId' => $friendlyName,
            'capabilities' => $capabilities,
            'metadata' => [
                'linkquality' => $payload['linkquality'] ?? null,
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
        return 'Zigbee2MQTT adapter for Zigbee devices via MQTT bridge';
    }

    public function supportsCorrelation(): bool
    {
        return true;
    }

    /**
     * Send bridge command (group management, etc.)
     *
     * Bridge commands use different MQTT topics:
     * - zigbee2mqtt/bridge/request/group/add
     * - zigbee2mqtt/bridge/request/group/members/add
     * - etc.
     */
    private function sendBridgeCommand(
        string $action,
        array $parameters,
        ExecutionMode $mode,
        ?CorrelationId $correlationId
    ): null {
        // Map action to bridge API endpoint
        $topic = match ($action) {
            // Group management
            'bridge_group_add' => 'zigbee2mqtt/bridge/request/group/add',
            'bridge_group_members_add' => 'zigbee2mqtt/bridge/request/group/members/add',
            'bridge_group_remove' => 'zigbee2mqtt/bridge/request/group/remove',
            'bridge_group_members_remove' => 'zigbee2mqtt/bridge/request/group/members/remove',

            // Scene management
            'bridge_scene_add' => 'zigbee2mqtt/bridge/request/scene/add',
            'bridge_scene_store' => 'zigbee2mqtt/bridge/request/scene/store',
            'bridge_scene_recall' => 'zigbee2mqtt/bridge/request/scene/recall',
            'bridge_scene_remove' => 'zigbee2mqtt/bridge/request/scene/remove',

            default => throw new InvalidArgumentException(sprintf('Unknown bridge action: %s', $action)),
        };

        // Parameters are sent as-is to the bridge
        $this->mqttPublisher->publish($topic, $parameters);

        // Bridge commands are always FIRE_AND_FORGET
        return null;
    }

    /**
     * Build command payload based on action and parameters
     *
     * Zigbee2MQTT Command Payloads Reference
     * @see https://www.zigbee2mqtt.io/guide/usage/mqtt_topics_and_messages.html
     *
     * Lighting:
     * - state: 'ON', 'OFF', 'TOGGLE'
     * - brightness: 0-254
     * - color_temp: 150-500 (mired), or use kelvin
     * - color: {r, g, b} OR {h, s, v} OR {x, y} OR {hex: '#RRGGBB'}
     * - effect: 'blink', 'breathe', 'okay', 'channel_change', 'finish_effect', 'colorloop'
     * - transition: time in seconds
     *
     * Covers:
     * - state: 'OPEN', 'CLOSE', 'STOP'
     * - position: 0-100 (0=closed, 100=open)
     * - tilt: 0-100
     *
     * Climate:
     * - system_mode: 'off', 'heat', 'cool', 'auto', 'dry', 'fan_only'
     * - occupied_heating_setpoint: temperature in °C
     * - occupied_cooling_setpoint: temperature in °C
     * - fan_mode: 'off', 'low', 'medium', 'high', 'auto'
     */
    private function buildCommandPayload(string $action, array $parameters): array
    {
        return match ($action) {
            'turn_on', 'start_watering' => ['state' => 'ON'],
            'turn_off', 'stop_watering' => ['state' => 'OFF'],
            'toggle' => ['state' => 'TOGGLE'],
            'set_brightness' => [
                'brightness' => min(254, max(0, $parameters['brightness'] ?? 255)),
                'transition' => $parameters['transition'] ?? null,
            ],
            'increase_brightness' => [
                'brightness_step' => $parameters['step'] ?? 50,
                'transition' => $parameters['transition'] ?? null,
            ],
            'decrease_brightness' => [
                // @phpstan-ignore-next-line cast.int
                'brightness_step' => -(isset($parameters['step']) ? (int) $parameters['step'] : 50),
                'transition' => $parameters['transition'] ?? null,
            ],
            'set_color', 'set_color_rgb' => [
                'color' => [
                    'r' => min(255, max(0, $parameters['r'] ?? 255)),
                    'g' => min(255, max(0, $parameters['g'] ?? 255)),
                    'b' => min(255, max(0, $parameters['b'] ?? 255)),
                ],
                'transition' => $parameters['transition'] ?? null,
            ],
            'set_color_hsv' => [
                'color' => [
                    'h' => min(360, max(0, $parameters['h'] ?? 0)),
                    's' => min(100, max(0, $parameters['s'] ?? 100)),
                    'v' => min(100, max(0, $parameters['v'] ?? 100)),
                ],
                'transition' => $parameters['transition'] ?? null,
            ],
            'set_hue' => [
                'color' => ['hue' => min(360, max(0, $parameters['hue'] ?? 0))],
                'transition' => $parameters['transition'] ?? null,
            ],
            'set_saturation' => [
                'color' => ['saturation' => min(100, max(0, $parameters['saturation'] ?? 100))],
                'transition' => $parameters['transition'] ?? null,
            ],
            'set_color_xy' => [
                'color' => [
                    'x' => min(1.0, max(0.0, $parameters['x'] ?? 0.5)),
                    'y' => min(1.0, max(0.0, $parameters['y'] ?? 0.5)),
                ],
                'transition' => $parameters['transition'] ?? null,
            ],
            'set_color_hex' => [
                'color' => ['hex' => $parameters['hex'] ?? '#FFFFFF'],
                'transition' => $parameters['transition'] ?? null,
            ],
            'set_color_temp', 'set_color_temperature' => [
                'color_temp' => min(500, max(150, $parameters['color_temp'] ?? 250)),
                'transition' => $parameters['transition'] ?? null,
            ],
            'set_color_temperature_mired' => [
                'color_temp' => min(500, max(150, $parameters['mired'] ?? 250)),
                'transition' => $parameters['transition'] ?? null,
            ],
            // White temperature presets
            'set_warm_white' => [
                'color_temp' => 500, // Warm (2000K)
                'transition' => $parameters['transition'] ?? null,
            ],
            'set_neutral_white' => [
                'color_temp' => 325, // Neutral (3077K)
                'transition' => $parameters['transition'] ?? null,
            ],
            'set_cool_white' => [
                'color_temp' => 150, // Cool (6667K)
                'transition' => $parameters['transition'] ?? null,
            ],
            'set_effect' => [
                'effect' => $parameters['effect'] ?? 'blink',
            ],
            'start_color_loop', 'start_colorloop' => [
                'effect' => 'colorloop',
            ],
            'stop_effect' => [
                'effect' => 'finish_effect',
            ],
            'blink' => ['effect' => 'blink'],
            'breathe' => ['effect' => 'breathe'],
            'open', 'open_valve' => ['state' => 'OPEN'],
            'close', 'close_valve' => ['state' => 'CLOSE'],
            'stop', 'stop_cleaning' => ['state' => 'STOP'],
            'set_position', 'open_to_position', 'close_to_position' => [
                'position' => min(100, max(0, $parameters['position'] ?? 50)),
            ],
            'set_tilt', 'open_tilt', 'close_tilt' => [
                'tilt' => min(100, max(0, $parameters['tilt'] ?? 50)),
            ],
            'set_valve_position' => [
                'position' => min(100, max(0, $parameters['position'] ?? 0)),
            ],
            'set_thermostat_mode', 'set_mode_off', 'set_mode_heat', 'set_mode_cool', 'set_mode_auto' => [
                'system_mode' => $parameters['mode'] ?? match ($action) {
                    'set_mode_off' => 'off',
                    'set_mode_heat' => 'heat',
                    'set_mode_cool' => 'cool',
                    'set_mode_auto' => 'auto',
                    default => $parameters['mode'] ?? 'auto',
                },
            ],
            'set_heating_setpoint' => [
                'occupied_heating_setpoint' => $parameters['temperature'] ?? 20,
            ],
            'set_cooling_setpoint' => [
                'occupied_cooling_setpoint' => $parameters['temperature'] ?? 24,
            ],
            'increase_heating_setpoint' => [
                'occupied_heating_setpoint_step' => $parameters['step'] ?? 0.5,
            ],
            'decrease_heating_setpoint' => [
                // @phpstan-ignore-next-line cast.double
                'occupied_heating_setpoint_step' => -(isset($parameters['step']) ? (float) $parameters['step'] : 0.5),
            ],
            'increase_cooling_setpoint' => [
                'occupied_cooling_setpoint_step' => $parameters['step'] ?? 0.5,
            ],
            'decrease_cooling_setpoint' => [
                // @phpstan-ignore-next-line cast.double
                'occupied_cooling_setpoint_step' => -(isset($parameters['step']) ? (float) $parameters['step'] : 0.5),
            ],
            'set_fan_mode' => [
                'fan_mode' => $parameters['mode'] ?? 'auto',
            ],
            'set_fan_auto' => [
                'fan_mode' => 'auto',
            ],
            'set_fan_on', 'set_oscillation', 'enable_oscillation' => [
                'fan_mode' => 'on',
            ],
            'set_fan_speed' => [
                'fan_mode' => $parameters['speed'] ?? 'medium',
            ],
            'set_fan_speed_percent' => [
                'fan_mode' => min(100, max(0, $parameters['percent'] ?? 50)),
            ],
            'increase_fan_speed' => [
                'fan_mode' => 'high',
            ],
            'decrease_fan_speed' => [
                'fan_mode' => 'low',
            ],
            'disable_oscillation' => [
                'fan_mode' => 'off',
            ],
            'set_humidity_target' => [
                'humidity' => min(100, max(0, $parameters['humidity'] ?? 50)),
            ],
            'increase_humidity_target' => [
                'humidity_step' => $parameters['step'] ?? 5,
            ],
            'decrease_humidity_target' => [
                // @phpstan-ignore-next-line cast.int
                'humidity_step' => -(isset($parameters['step']) ? (int) $parameters['step'] : 5),
            ],
            'set_purifier_mode' => [
                'mode' => $parameters['mode'] ?? 'auto',
            ],
            'set_purifier_speed', 'set_vacuum_fan_speed' => [
                'fan_speed' => $parameters['speed'] ?? 'medium',
            ],
            'increase_purifier_speed' => [
                'fan_speed' => 'high',
            ],
            'decrease_purifier_speed' => [
                'fan_speed' => 'low',
            ],
            'reset_energy' => [
                'energy' => 0,
            ],
            'identify' => [
                'identify' => ['duration' => $parameters['duration'] ?? 10],
            ],
            'check_update' => [
                'update' => ['check' => true],
            ],
            'start_update' => [
                'update' => ['update' => true],
            ],
            'configure' => [
                'configure_reporting' => true,
            ],
            'reset_configuration' => [
                'reset' => true,
            ],
            'lock' => ['state' => 'LOCK'],
            'unlock' => ['state' => 'UNLOCK'],

            'unlock_with_timeout' => [
                'state' => 'UNLOCK',
                'timeout' => $parameters['timeout'] ?? 30,
            ],
            'arm', 'arm_away' => ['state' => 'ARM_AWAY'],
            'arm_home' => ['state' => 'ARM_HOME'],
            'arm_night' => ['state' => 'ARM_NIGHT'],
            'disarm' => ['state' => 'DISARM'],
            'trigger' => ['state' => 'TRIGGER'],
            'start_cleaning' => ['state' => 'START'],
            'pause_cleaning' => ['state' => 'PAUSE'],
            'return_to_base' => ['state' => 'RETURN_TO_BASE'],
            'locate_vacuum' => ['locate' => true],
            'clean_spot' => ['state' => 'SPOT'],
            'clean_zone' => [
                'clean_zone' => $parameters['zone'] ?? [],
            ],
            'set_watering_duration' => [
                'duration' => $parameters['duration'] ?? 10,
            ],
            'set_watering_schedule' => [
                'schedule' => $parameters['schedule'] ?? [],
            ],
            'send_notification' => [
                'message' => $parameters['message'] ?? 'Notification',
            ],
            'play_sound' => [
                'sound' => $parameters['sound'] ?? 'default',
            ],
            'flash_light' => [
                'effect' => 'blink',
            ],
            'activate_scene', 'recall_scene', 'store_scene', 'delete_scene' => [
                'scene' => $parameters['scene'] ?? $parameters['scene_id'] ?? 1,
            ],

            default => array_filter($parameters, fn ($v) => $v !== null),
        };
    }
}
