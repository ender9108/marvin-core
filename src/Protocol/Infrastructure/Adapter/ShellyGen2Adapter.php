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

use Exception;
use Marvin\Protocol\Domain\Model\ProtocolAdapterInterface;
use Marvin\Protocol\Domain\ValueObject\ExecutionMode;
use Marvin\Protocol\Infrastructure\Protocol\JsonRpcProtocol;
use Marvin\Shared\Domain\ValueObject\Identity\CorrelationId;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Adapter for Shelly Gen2/Gen3 devices
 * Handles Shelly Plus/Pro devices via JSON-RPC over HTTP
 */
final readonly class ShellyGen2Adapter implements ProtocolAdapterInterface
{
    private JsonRpcProtocol $jsonRpcProtocol;

    public function __construct(
        HttpClientInterface $httpClient,
    ) {
        $this->jsonRpcProtocol = new JsonRpcProtocol($httpClient);
    }

    public function getName(): string
    {
        return 'shelly_gen2';
    }

    public function getProtocolType(): string
    {
        return 'jsonrpc';
    }

    public function getSupportedProtocols(): array
    {
        return ['jsonrpc', 'rest', 'network', 'http'];
    }

    public function supports(string $protocol, array $deviceMetadata = []): bool
    {
        $manufacturer = strtolower($deviceMetadata['manufacturer'] ?? '');
        $model = strtolower($deviceMetadata['model'] ?? '');

        return ($protocol === 'jsonrpc' || $protocol === 'rest' || $protocol === 'http')
            && $manufacturer === 'shelly'
            && (str_contains($model, 'plus') || str_contains($model, 'pro'));
    }

    public function sendCommand(
        string $nativeId,
        string $action,
        array $parameters = [],
        ExecutionMode $mode = ExecutionMode::DEVICE_LOCK,
        ?CorrelationId $correlationId = null
    ): ?array {
        // nativeId is the device IP address or hostname (e.g., "192.168.1.100" or "shellyplus1-ABC123")
        // Already resolved by DeviceQueryService in SendDeviceCommandHandler or ProtocolCapabilityService
        $rpcUrl = "http://$nativeId/rpc";

        // Build JSON-RPC method and params
        [$method, $params] = $this->buildRpcCall($action, $parameters);

        // Use correlation ID if provided
        $requestId = $correlationId?->toString() ?? uniqid('rpc_');

        try {
            $response = $this->jsonRpcProtocol->call(
                $rpcUrl,
                $method,
                $params,
                $requestId
            );

            if ($mode === ExecutionMode::FIRE_AND_FORGET) {
                return null;
            }

            return $this->transformResponse($response);
        } catch (Exception $e) {
            if ($mode === ExecutionMode::FIRE_AND_FORGET) {
                return null;
            }

            throw $e;
        }
    }

    public function transformMessage(string $topic, array $payload): ?array
    {
        // JSON-RPC over HTTP doesn't use topics
        // This could be used for WebSocket variant
        return null;
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
        return 'Shelly Gen2/Gen3 adapter for Shelly Plus/Pro devices via JSON-RPC';
    }

    public function supportsCorrelation(): bool
    {
        return true;
    }

    /**
     * Build Shelly Gen2 JSON-RPC call
     *
     * Shelly Gen2/Gen3 RPC API Reference:
     * @see https://shelly-api-docs.shelly.cloud/gen2/
     *
     * Common RPC methods:
     * - Switch.Set - Control switch (id, on)
     * - Switch.Toggle - Toggle switch (id)
     * - Light.Set - Control light (id, on, brightness, rgb, temperature)
     * - Cover.Open/Close/Stop - Control roller shutter (id)
     * - Cover.GoToPosition - Set shutter position (id, pos)
     *
     * @return array{string, array<string, mixed>} [method, params]
     */
    private function buildRpcCall(string $action, array $parameters): array
    {
        $switchId = (int) ($parameters['switch_id'] ?? 0);
        $lightId = (int) ($parameters['light_id'] ?? 0);
        $coverId = (int) ($parameters['cover_id'] ?? 0);

        return match ($action) {
            // ========================================
            // BASIC SWITCH
            // ========================================
            'turn_on' => [
                'Switch.Set',
                ['id' => $switchId, 'on' => true],
            ],
            'turn_off' => [
                'Switch.Set',
                ['id' => $switchId, 'on' => false],
            ],
            'toggle' => [
                'Switch.Toggle',
                ['id' => $switchId],
            ],

            // ========================================
            // BRIGHTNESS
            // ========================================
            'set_brightness' => [
                'Light.Set',
                [
                    'id' => $lightId,
                    'on' => true,
                    'brightness' => min(100, max(0, $parameters['brightness'] ?? 100)),
                ],
            ],

            'increase_brightness' => [
                'Light.Set',
                [
                    'id' => $lightId,
                    'brightness' => min(100, (int) ($parameters['current'] ?? 50) + (int) ($parameters['step'] ?? 10)),
                ],
            ],

            'decrease_brightness' => [
                'Light.Set',
                [
                    'id' => $lightId,
                    'brightness' => max(0, (int) ($parameters['current'] ?? 50) - (int) ($parameters['step'] ?? 10)),
                ],
            ],

            // ========================================
            // COLOR - RGB
            // ========================================
            'set_color', 'set_color_rgb' => [
                'Light.Set',
                [
                    'id' => $lightId,
                    'on' => true,
                    'rgb' => [
                        min(255, max(0, $parameters['r'] ?? 255)),
                        min(255, max(0, $parameters['g'] ?? 255)),
                        min(255, max(0, $parameters['b'] ?? 255)),
                    ],
                ],
            ],

            'set_color_hex' => [
                'Light.Set',
                [
                    'id' => $lightId,
                    'on' => true,
                    'rgb' => [
                        hexdec(substr((string) str_replace('#', '', $parameters['hex'] ?? 'FFFFFF'), 0, 2)),
                        hexdec(substr((string) str_replace('#', '', $parameters['hex'] ?? 'FFFFFF'), 2, 2)),
                        hexdec(substr((string) str_replace('#', '', $parameters['hex'] ?? 'FFFFFF'), 4, 2)),
                    ],
                ],
            ],

            // ========================================
            // COLOR TEMPERATURE
            // ========================================
            'set_color_temp', 'set_color_temperature' => [
                'Light.Set',
                [
                    'id' => $lightId,
                    'on' => true,
                    'temperature' => min(6500, max(2700, $parameters['temp'] ?? 4000)),
                ],
            ],

            'set_warm_white' => [
                'Light.Set',
                ['id' => $lightId, 'on' => true, 'temperature' => 2700],
            ],

            'set_neutral_white' => [
                'Light.Set',
                ['id' => $lightId, 'on' => true, 'temperature' => 4000],
            ],

            'set_cool_white' => [
                'Light.Set',
                ['id' => $lightId, 'on' => true, 'temperature' => 6500],
            ],

            // ========================================
            // LIGHT EFFECTS
            // ========================================
            'set_effect' => [
                'Light.Set',
                [
                    'id' => $lightId,
                    'effect' => $parameters['effect'] ?? 0,
                ],
            ],

            'stop_effect' => [
                'Light.Set',
                ['id' => $lightId, 'effect' => 0],
            ],

            // ========================================
            // ROLLER SHUTTERS / COVERS
            // ========================================
            'open' => [
                'Cover.Open',
                ['id' => $coverId],
            ],

            'close' => [
                'Cover.Close',
                ['id' => $coverId],
            ],

            'stop' => [
                'Cover.Stop',
                ['id' => $coverId],
            ],

            'set_position', 'open_to_position', 'close_to_position' => [
                'Cover.GoToPosition',
                [
                    'id' => $coverId,
                    'pos' => min(100, max(0, $parameters['position'] ?? 50)),
                ],
            ],

            // ========================================
            // ENERGY MONITORING
            // ========================================
            'reset_energy' => [
                'Switch.ResetCounters',
                [
                    'id' => $switchId,
                    'type' => ['aenergy'],
                ],
            ],

            // ========================================
            // SYSTEM
            // ========================================
            'identify' => [
                'Shelly.SetProfile',
                ['name' => 'identify'],
            ],

            'configure' => [
                'Shelly.GetConfig',
                [],
            ],

            'reset_configuration' => [
                'Shelly.FactoryReset',
                [],
            ],

            'get_status' => [
                'Shelly.GetStatus',
                [],
            ],

            'get_switch_status' => [
                'Switch.GetStatus',
                ['id' => $switchId],
            ],

            // ========================================
            // DEFAULT - Pass-through
            // ========================================
            default => [
                $action,
                $parameters,
            ],
        };
    }

    /**
     * Transform Shelly Gen2 JSON-RPC response to capabilities
     */
    private function transformResponse(array $response): array
    {
        $capabilities = [];
        $result = $response['result'] ?? [];

        // Extract switch state
        if (isset($result['output'])) {
            $capabilities[] = [
                'name' => 'switch',
                'value' => $result['output'],
            ];
        }

        // Extract power measurement
        if (isset($result['apower'])) {
            $capabilities[] = [
                'name' => 'power',
                'value' => $result['apower'],
            ];
        }

        return [
            'capabilities' => $capabilities,
            'raw_data' => $result,
        ];
    }
}
