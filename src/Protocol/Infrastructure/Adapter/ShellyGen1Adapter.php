<?php

declare(strict_types=1);

namespace Marvin\Protocol\Infrastructure\Adapter;

use Exception;
use Marvin\Protocol\Domain\Model\ProtocolAdapterInterface;
use Marvin\Protocol\Domain\ValueObject\ExecutionMode;
use Marvin\Protocol\Infrastructure\Protocol\RestProtocol;
use Marvin\Shared\Domain\ValueObject\Identity\CorrelationId;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Adapter for Shelly Gen1 devices
 * Handles Shelly Gen1 devices via REST API
 */
final readonly class ShellyGen1Adapter implements ProtocolAdapterInterface
{
    private RestProtocol $restProtocol;

    public function __construct(
        HttpClientInterface $httpClient,
    ) {
        $this->restProtocol = new RestProtocol($httpClient);
    }

    public function getName(): string
    {
        return 'shelly_gen1';
    }

    public function getProtocolType(): string
    {
        return 'rest';
    }

    public function getSupportedProtocols(): array
    {
        return ['rest', 'network', 'http'];
    }

    public function supports(string $protocol, array $deviceMetadata = []): bool
    {
        $manufacturer = strtolower($deviceMetadata['manufacturer'] ?? '');
        $model = strtolower($deviceMetadata['model'] ?? '');

        return ($protocol === 'rest' || $protocol === 'http')
            && $manufacturer === 'shelly'
            && !str_contains($model, 'plus') // Gen1 doesn't have "plus" in model
            && !str_contains($model, 'pro');  // Gen1 doesn't have "pro" in model
    }

    public function sendCommand(
        string $nativeId,
        string $action,
        array $parameters = [],
        ExecutionMode $mode = ExecutionMode::DEVICE_LOCK,
        ?CorrelationId $correlationId = null
    ): ?array {
        // nativeId is the device IP address or hostname (e.g., "192.168.1.100" or "shelly1-ABC123")
        // Already resolved by DeviceQueryService in SendDeviceCommandHandler or ProtocolCapabilityService
        $baseUrl = "http://$nativeId";

        // Build REST endpoint and query params
        [$endpoint, $queryParams] = $this->buildCommand($action, $parameters);

        $url = $baseUrl . $endpoint;

        if (!empty($queryParams)) {
            $url .= '?' . http_build_query($queryParams);
        }

        // Send REST request
        try {
            $response = $this->restProtocol->get($url);
            $data = $response->toArray();

            if ($mode === ExecutionMode::FIRE_AND_FORGET) {
                return null;
            }

            return $this->transformResponse($data);
        } catch (Exception $e) {
            if ($mode === ExecutionMode::FIRE_AND_FORGET) {
                return null;
            }

            throw $e;
        }
    }

    public function transformMessage(string $topic, array $payload): ?array
    {
        // Shelly Gen1 REST adapter doesn't use topics
        // This method is for polling/webhook scenarios
        return null;
    }

    public function getDefaultExecutionMode(): ExecutionMode
    {
        return ExecutionMode::DEVICE_LOCK;
    }

    public function getSupportedExecutionModes(): array
    {
        return [
            ExecutionMode::DEVICE_LOCK->value,
            ExecutionMode::FIRE_AND_FORGET->value,
        ];
    }

    public function getDescription(): string
    {
        return 'Shelly Gen1 adapter for Shelly devices via REST API';
    }

    public function supportsCorrelation(): bool
    {
        return false;
    }

    /**
     * Build Shelly Gen1 REST command
     *
     * Shelly Gen1 REST API Reference:
     * @see https://shelly-api-docs.shelly.cloud/gen1/
     *
     * Common endpoints:
     * - /relay/{id} - Control relay (turn: on/off/toggle, timer)
     * - /light/{id} - Control light (turn, brightness, rgb, white, temp, effect)
     * - /roller/{id} - Control roller shutter (go: open/close/stop, pos, duration)
     * - /color/{id} - Control RGBW (red, green, blue, white, gain, brightness, temp, effect)
     * - /meter/{id} - Get power measurement
     *
     * @return array{string, array<string, mixed>} [endpoint, queryParams]
     */
    private function buildCommand(string $action, array $parameters): array
    {
        $relay = (int) ($parameters['relay'] ?? 0);
        $light = (int) ($parameters['light'] ?? 0);
        $roller = (int) ($parameters['roller'] ?? 0);

        return match ($action) {
            // ========================================
            // BASIC SWITCH & RELAY
            // ========================================
            'turn_on' => [
                "/relay/$relay",
                ['turn' => 'on'],
            ],
            'turn_off' => [
                "/relay/$relay",
                ['turn' => 'off'],
            ],
            'toggle' => [
                "/relay/$relay",
                ['turn' => 'toggle'],
            ],

            // ========================================
            // BRIGHTNESS (Dimmers)
            // ========================================
            'set_brightness' => [
                "/light/$light",
                [
                    'turn' => 'on',
                    'brightness' => min(100, max(0, $parameters['brightness'] ?? 100)),
                ],
            ],

            'increase_brightness' => [
                "/light/$light",
                ['brightness' => min(100, (int) ($parameters['current'] ?? 50) + (int) ($parameters['step'] ?? 10))],
            ],

            'decrease_brightness' => [
                "/light/$light",
                ['brightness' => max(0, (int) ($parameters['current'] ?? 50) - (int) ($parameters['step'] ?? 10))],
            ],

            // ========================================
            // COLOR - RGB/RGBW
            // ========================================
            'set_color', 'set_color_rgb' => [
                "/color/$light",
                [
                    'turn' => 'on',
                    'red' => min(255, max(0, $parameters['r'] ?? 255)),
                    'green' => min(255, max(0, $parameters['g'] ?? 255)),
                    'blue' => min(255, max(0, $parameters['b'] ?? 255)),
                    'gain' => min(100, max(0, $parameters['gain'] ?? 100)),
                ],
            ],

            'set_color_hex' => [
                "/color/$light",
                [
                    'turn' => 'on',
                    'red' => hexdec(substr((string) str_replace('#', '', $parameters['hex'] ?? 'FFFFFF'), 0, 2)),
                    'green' => hexdec(substr((string) str_replace('#', '', $parameters['hex'] ?? 'FFFFFF'), 2, 2)),
                    'blue' => hexdec(substr((string) str_replace('#', '', $parameters['hex'] ?? 'FFFFFF'), 4, 2)),
                ],
            ],

            // ========================================
            // COLOR TEMPERATURE & WHITE
            // ========================================
            'set_color_temp', 'set_color_temperature' => [
                "/light/$light",
                [
                    'turn' => 'on',
                    'temp' => min(6500, max(3000, $parameters['temp'] ?? 4000)),
                ],
            ],

            'set_warm_white' => [
                "/light/$light",
                ['turn' => 'on', 'temp' => 3000],
            ],

            'set_neutral_white' => [
                "/light/$light",
                ['turn' => 'on', 'temp' => 4000],
            ],

            'set_cool_white' => [
                "/light/$light",
                ['turn' => 'on', 'temp' => 6500],
            ],

            // ========================================
            // LIGHT EFFECTS
            // ========================================
            'set_effect' => [
                "/color/$light",
                [
                    'effect' => min(3, max(0, $parameters['effect'] ?? 0)),
                ],
            ],

            'stop_effect' => [
                "/color/$light",
                ['effect' => 0],
            ],

            // ========================================
            // ROLLER SHUTTERS
            // ========================================
            'open' => [
                "/roller/$roller",
                ['go' => 'open'],
            ],

            'close' => [
                "/roller/$roller",
                ['go' => 'close'],
            ],

            'stop' => [
                "/roller/$roller",
                ['go' => 'stop'],
            ],

            'set_position', 'open_to_position', 'close_to_position' => [
                "/roller/$roller",
                [
                    'go' => 'to_pos',
                    'roller_pos' => min(100, max(0, $parameters['position'] ?? 50)),
                ],
            ],

            // ========================================
            // ENERGY MONITORING
            // ========================================
            'reset_energy' => [
                "/meter/$relay",
                ['reset' => '1'],
            ],

            // ========================================
            // SYSTEM
            // ========================================
            'identify' => [
                "/relay/$relay",
                ['turn' => 'toggle'],
            ],

            'configure' => [
                '/status',
                [],
            ],

            'reset_configuration' => [
                '/reboot',
                ['reset' => '1'],
            ],

            'get_status' => [
                '/status',
                [],
            ],

            // ========================================
            // DEFAULT - Pass-through
            // ========================================
            default => [
                "/$action",
                $parameters,
            ],
        };
    }

    /**
     * Transform Shelly Gen1 REST response to capabilities
     */
    private function transformResponse(array $data): array
    {
        $capabilities = [];

        // Extract relay state
        if (isset($data['ison'])) {
            $capabilities[] = [
                'name' => 'switch',
                'value' => $data['ison'],
            ];
        }

        // Extract power measurement
        if (isset($data['power'])) {
            $capabilities[] = [
                'name' => 'power',
                'value' => $data['power'],
            ];
        }

        return [
            'capabilities' => $capabilities,
            'raw_data' => $data,
        ];
    }
}
