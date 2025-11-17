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

namespace Marvin\Device\Application\CommandHandler\Device;

use Marvin\Device\Application\Command\Device\CreateVirtualDevice;
use Marvin\Device\Domain\Exception\InvalidVirtualConfig;
use Marvin\Device\Domain\Model\Device;
use Marvin\Device\Domain\Repository\DeviceRepositoryInterface;
use Marvin\Device\Domain\ValueObject\VirtualDeviceType;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * Handler for CreateVirtualDevice command
 *
 * Creates a new virtual device (TIME, WEATHER, HTTP) and validates:
 * - Configuration is valid for the virtual device type
 * - Required configuration keys are present
 */
#[AsMessageHandler]
final readonly class CreateVirtualDeviceHandler
{
    public function __construct(
        private DeviceRepositoryInterface $deviceRepository,
        private LoggerInterface $logger
    ) {
    }

    public function __invoke(CreateVirtualDevice $command): Device
    {
        $this->logger->info('Creating virtual device', [
            'label' => $command->label->value,
            'virtualType' => $command->virtualType->value,
        ]);

        // Validate configuration based on virtual device type
        $this->validateConfiguration($command->virtualType, $command->virtualConfig);

        // Create device using factory method
        $device = Device::createVirtual(
            label: $command->label,
            virtualType: $command->virtualType,
            virtualConfig: $command->virtualConfig,
            capabilities: $command->capabilities,
            zoneId: $command->zoneId,
            description: $command->description,
            metadata: $command->metadata,
        );

        // Save device
        $this->deviceRepository->save($device);

        $this->logger->info('Virtual device created successfully', [
            'deviceId' => $device->id->toString(),
            'label' => $device->label->value,
            'virtualType' => $command->virtualType->value,
            'capabilitiesCount' => count($device->capabilities),
        ]);

        return $device;
    }

    /**
     * Validate configuration based on virtual device type
     */
    private function validateConfiguration(VirtualDeviceType $type, $config): void
    {
        match ($type) {
            // Time-based
            VirtualDeviceType::TIME_TRIGGER => $this->validateTimeTriggerConfig($config),
            VirtualDeviceType::SUN_TRIGGER => $this->validateSunTriggerConfig($config),
            VirtualDeviceType::TIMER => $this->validateTimerConfig($config),
            VirtualDeviceType::COUNTER => $this->validateCounterConfig($config),

            // Weather
            VirtualDeviceType::WEATHER => $this->validateWeatherConfig($config),
            VirtualDeviceType::WEATHER_ALERT => $this->validateWeatherAlertConfig($config),

            // Network & System
            VirtualDeviceType::HTTP_TRIGGER => $this->validateHttpTriggerConfig($config),
            VirtualDeviceType::MQTT_VIRTUAL => $this->validateMqttVirtualConfig($config),
            VirtualDeviceType::PRESENCE_VIRTUAL => $this->validatePresenceVirtualConfig($config),
            VirtualDeviceType::DEVICE_TRACKER => $this->validateDeviceTrackerConfig($config),

            // Variables & Storage
            VirtualDeviceType::VARIABLE => $this->validateVariableConfig($config),
            VirtualDeviceType::STORAGE => $this->validateStorageConfig($config),

            // Logic
            VirtualDeviceType::CONDITION => $this->validateConditionConfig($config),
            VirtualDeviceType::SCENE => $this->validateSceneConfig($config),
            VirtualDeviceType::SCRIPT => $this->validateScriptConfig($config),

            // Notifications
            VirtualDeviceType::NOTIFIER => $this->validateNotifierConfig($config),
            VirtualDeviceType::TTS => $this->validateTtsConfig($config),

            // External Integrations
            VirtualDeviceType::CALENDAR => $this->validateCalendarConfig($config),
            VirtualDeviceType::RSS_FEED => $this->validateRssFeedConfig($config),
        };
    }

    // ========== Time-based Validators ==========

    private function validateTimeTriggerConfig($config): void
    {
        if (!$config->has('times')) {
            throw InvalidVirtualConfig::missingKeys('TIME_TRIGGER', ['times']);
        }

        $times = $config->get('times');
        if (!is_array($times) || empty($times)) {
            throw InvalidVirtualConfig::invalidValue('times', 'Must be a non-empty array of HH:MM format');
        }

        foreach ($times as $time) {
            if (!preg_match('/^([01]\d|2[0-3]):([0-5]\d)$/', (string) $time)) {
                throw InvalidVirtualConfig::invalidValue('times', "Invalid time format: $time (expected HH:MM)");
            }
        }
    }

    private function validateSunTriggerConfig($config): void
    {
        if (!$config->has('latitude') || !$config->has('longitude')) {
            throw InvalidVirtualConfig::missingKeys('SUN_TRIGGER', ['latitude', 'longitude']);
        }

        $lat = $config->get('latitude');
        $lon = $config->get('longitude');

        if (!is_numeric($lat) || $lat < -90 || $lat > 90) {
            throw InvalidVirtualConfig::invalidValue('latitude', 'Must be between -90 and 90');
        }

        if (!is_numeric($lon) || $lon < -180 || $lon > 180) {
            throw InvalidVirtualConfig::invalidValue('longitude', 'Must be between -180 and 180');
        }
    }

    private function validateTimerConfig($config): void
    {
        if (!$config->has('duration')) {
            throw InvalidVirtualConfig::missingKeys('TIMER', ['duration']);
        }

        $duration = $config->get('duration');
        if (!is_int($duration) || $duration <= 0) {
            throw InvalidVirtualConfig::invalidValue('duration', 'Must be a positive integer (seconds)');
        }
    }

    private function validateCounterConfig($config): void
    {
        if ($config->has('initial_value')) {
            $initial = $config->get('initial_value');
            if (!is_int($initial) || $initial < 0) {
                throw InvalidVirtualConfig::invalidValue('initial_value', 'Must be a non-negative integer');
            }
        }

        if ($config->has('max_value')) {
            $max = $config->get('max_value');
            if (!is_int($max) || $max <= 0) {
                throw InvalidVirtualConfig::invalidValue('max_value', 'Must be a positive integer');
            }
        }
    }

    // ========== Weather Validators ==========

    private function validateWeatherConfig($config): void
    {
        if (!$config->has('provider')) {
            throw InvalidVirtualConfig::missingKeys('WEATHER', ['provider']);
        }

        $provider = $config->get('provider');
        $allowedProviders = ['openweathermap', 'weatherapi', 'meteo_france'];

        if (!in_array($provider, $allowedProviders, true)) {
            throw InvalidVirtualConfig::invalidValue(
                'provider',
                sprintf('Must be one of: %s', implode(', ', $allowedProviders))
            );
        }

        if ($provider !== 'meteo_france' && !$config->has('api_key')) {
            throw InvalidVirtualConfig::missingKeys('WEATHER', ['api_key']);
        }

        if (!$config->has('city') && (!$config->has('latitude') || !$config->has('longitude'))) {
            throw InvalidVirtualConfig::missingKeys('WEATHER', ['city or (latitude + longitude)']);
        }
    }

    private function validateWeatherAlertConfig($config): void
    {
        $this->validateWeatherConfig($config);

        if ($config->has('alert_types')) {
            $alertTypes = $config->get('alert_types');
            if (!is_array($alertTypes)) {
                throw InvalidVirtualConfig::invalidValue('alert_types', 'Must be an array');
            }
        }
    }

    // ========== Network & System Validators ==========

    private function validateHttpTriggerConfig($config): void
    {
        if (!$config->has('endpoint')) {
            throw InvalidVirtualConfig::missingKeys('HTTP_TRIGGER', ['endpoint']);
        }

        $endpoint = $config->get('endpoint');
        if (empty($endpoint) || !is_string($endpoint)) {
            throw InvalidVirtualConfig::invalidValue('endpoint', 'Must be a non-empty string');
        }
    }

    private function validateMqttVirtualConfig($config): void
    {
        if (!$config->has('topic')) {
            throw InvalidVirtualConfig::missingKeys('MQTT_VIRTUAL', ['topic']);
        }

        $topic = $config->get('topic');
        if (empty($topic) || !is_string($topic)) {
            throw InvalidVirtualConfig::invalidValue('topic', 'Must be a non-empty string');
        }
    }

    private function validatePresenceVirtualConfig($config): void
    {
        if (!$config->has('device_ids')) {
            throw InvalidVirtualConfig::missingKeys('PRESENCE_VIRTUAL', ['device_ids']);
        }

        $deviceIds = $config->get('device_ids');
        if (!is_array($deviceIds) || empty($deviceIds)) {
            throw InvalidVirtualConfig::invalidValue('device_ids', 'Must be a non-empty array');
        }

        if ($config->has('timeout')) {
            $timeout = $config->get('timeout');
            if (!is_int($timeout) || $timeout <= 0) {
                throw InvalidVirtualConfig::invalidValue('timeout', 'Must be a positive integer (seconds)');
            }
        }
    }

    private function validateDeviceTrackerConfig($config): void
    {
        if (!$config->has('method') || !$config->has('address')) {
            throw InvalidVirtualConfig::missingKeys('DEVICE_TRACKER', ['method', 'address']);
        }

        $method = $config->get('method');
        $allowedMethods = ['ping', 'bluetooth', 'arp'];

        if (!in_array($method, $allowedMethods, true)) {
            throw InvalidVirtualConfig::invalidValue(
                'method',
                sprintf('Must be one of: %s', implode(', ', $allowedMethods))
            );
        }
    }

    // ========== Variables & Storage Validators ==========

    private function validateVariableConfig($config): void
    {
        if ($config->has('type')) {
            $type = $config->get('type');
            $allowedTypes = ['string', 'int', 'float', 'bool', 'array', 'json'];

            if (!in_array($type, $allowedTypes, true)) {
                throw InvalidVirtualConfig::invalidValue(
                    'type',
                    sprintf('Must be one of: %s', implode(', ', $allowedTypes))
                );
            }
        }
    }

    private function validateStorageConfig($config): void
    {
        $this->validateVariableConfig($config);
    }

    // ========== Logic Validators ==========

    private function validateConditionConfig($config): void
    {
        if (!$config->has('condition')) {
            throw InvalidVirtualConfig::missingKeys('CONDITION', ['condition']);
        }

        $condition = $config->get('condition');
        if (empty($condition) || !is_string($condition)) {
            throw InvalidVirtualConfig::invalidValue('condition', 'Must be a non-empty expression string');
        }
    }

    private function validateSceneConfig($config): void
    {
        // SCENE is deprecated, log warning
        $this->logger->warning('VirtualDeviceType::SCENE is deprecated, use DeviceType::COMPOSITE with CompositeType::SCENE instead');
    }

    private function validateScriptConfig($config): void
    {
        if (!$config->has('actions')) {
            throw InvalidVirtualConfig::missingKeys('SCRIPT', ['actions']);
        }

        $actions = $config->get('actions');
        if (!is_array($actions) || empty($actions)) {
            throw InvalidVirtualConfig::invalidValue('actions', 'Must be a non-empty array of actions');
        }
    }

    // ========== Notifications Validators ==========

    private function validateNotifierConfig($config): void
    {
        if (!$config->has('provider')) {
            throw InvalidVirtualConfig::missingKeys('NOTIFIER', ['provider']);
        }

        $provider = $config->get('provider');
        $allowedProviders = ['email', 'push', 'sms', 'telegram', 'discord', 'slack'];

        if (!in_array($provider, $allowedProviders, true)) {
            throw InvalidVirtualConfig::invalidValue(
                'provider',
                sprintf('Must be one of: %s', implode(', ', $allowedProviders))
            );
        }
    }

    private function validateTtsConfig($config): void
    {
        if (!$config->has('provider')) {
            throw InvalidVirtualConfig::missingKeys('TTS', ['provider']);
        }

        $provider = $config->get('provider');
        $allowedProviders = ['google', 'amazon', 'azure', 'pico'];

        if (!in_array($provider, $allowedProviders, true)) {
            throw InvalidVirtualConfig::invalidValue(
                'provider',
                sprintf('Must be one of: %s', implode(', ', $allowedProviders))
            );
        }

        if ($config->has('language')) {
            $language = $config->get('language');
            if (empty($language) || !is_string($language)) {
                throw InvalidVirtualConfig::invalidValue('language', 'Must be a non-empty string (e.g., fr-FR)');
            }
        }
    }

    // ========== External Integrations Validators ==========

    private function validateCalendarConfig($config): void
    {
        if (!$config->has('provider')) {
            throw InvalidVirtualConfig::missingKeys('CALENDAR', ['provider']);
        }

        $provider = $config->get('provider');
        $allowedProviders = ['google', 'caldav', 'ical'];

        if (!in_array($provider, $allowedProviders, true)) {
            throw InvalidVirtualConfig::invalidValue(
                'provider',
                sprintf('Must be one of: %s', implode(', ', $allowedProviders))
            );
        }

        if ($provider === 'google' && !$config->has('calendar_id')) {
            throw InvalidVirtualConfig::missingKeys('CALENDAR', ['calendar_id']);
        }

        if ($provider === 'caldav' && !$config->has('url')) {
            throw InvalidVirtualConfig::missingKeys('CALENDAR', ['url']);
        }

        if ($provider === 'ical' && !$config->has('url')) {
            throw InvalidVirtualConfig::missingKeys('CALENDAR', ['url']);
        }
    }

    private function validateRssFeedConfig($config): void
    {
        if (!$config->has('url')) {
            throw InvalidVirtualConfig::missingKeys('RSS_FEED', ['url']);
        }

        $url = $config->get('url');
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw InvalidVirtualConfig::invalidValue('url', 'Must be a valid URL');
        }

        if ($config->has('update_interval')) {
            $interval = $config->get('update_interval');
            if (!is_int($interval) || $interval < 300) {
                throw InvalidVirtualConfig::invalidValue('update_interval', 'Must be at least 300 seconds (5 minutes)');
            }
        }
    }
}
