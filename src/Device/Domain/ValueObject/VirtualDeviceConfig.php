<?php

namespace Marvin\Device\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\Assert\Assert;

final readonly class VirtualDeviceConfig
{
    private array $config;

    private function __construct(array $config)
    {
        Assert::notEmpty($config, 'device.exceptions.DE0029.config_must_not_be_empty');;
        $this->config = $config;
    }

    public static function fromArray(array $config): self
    {
        return new self($config);
    }

    public static function forWeatherDevice(string $apiKey, string $location): self
    {
        return new self([
            'api_key' => $apiKey,
            'location' => $location,
            'update_interval' => 600, // 10 minutes
        ]);
    }

    public static function forHttpDevice(string $url, ?string $authToken = null): self
    {
        $config = ['url' => $url];
        if ($authToken) {
            $config['auth_token'] = $authToken;
        }
        return new self($config);
    }

    public function get(string $key): mixed
    {
        return $this->config[$key] ?? null;
    }

    public function has(string $key): bool
    {
        return isset($this->config[$key]);
    }

    public function toArray(): array
    {
        return $this->config;
    }
}
