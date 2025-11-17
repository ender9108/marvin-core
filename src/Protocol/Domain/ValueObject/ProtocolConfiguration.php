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

namespace Marvin\Protocol\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use EnderLab\DddCqrsBundle\Domain\ValueObject\ValueObjectTrait;

final readonly class ProtocolConfiguration
{
    use ValueObjectTrait;

    public array $value;

    public function __construct(array $value)
    {
        Assert::notEmpty($value, 'Protocol configuration cannot be empty');
        $this->value = $value;
    }

    public static function fromArray(array $value): self
    {
        return new self($value);
    }

    public static function mqtt(
        string $host,
        int $port,
        int $protocolLevel = 5,
        ?string $credentialsRef = null,
        int $qos = 1,
        bool $retain = false
    ): self {
        return new self([
            'host' => $host,
            'port' => $port,
            'protocol_level' => $protocolLevel,
            'credentials_ref' => $credentialsRef,
            'qos' => $qos,
            'retain' => $retain,
        ]);
    }

    public static function rest(
        string $baseUri,
        float $timeout = 5.0,
        ?string $credentialsRef = null,
        ?string $authType = null
    ): self {
        return new self([
            'base_uri' => $baseUri,
            'timeout' => $timeout,
            'credentials_ref' => $credentialsRef,
            'auth_type' => $authType,
        ]);
    }

    public static function jsonRpc(
        string $baseUri,
        float $timeout = 5.0,
        ?string $credentialsRef = null,
        ?string $authType = null
    ): self {
        return new self([
            'base_uri' => $baseUri,
            'timeout' => $timeout,
            'credentials_ref' => $credentialsRef,
            'auth_type' => $authType,
        ]);
    }

    public static function webSocket(
        string $url,
        bool $ssl = false,
        float $timeout = 5.0,
        ?string $credentialsRef = null,
        array $headers = []
    ): self {
        return new self([
            'url' => $url,
            'ssl' => $ssl,
            'timeout' => $timeout,
            'credentials_ref' => $credentialsRef,
            'headers' => $headers,
        ]);
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->value[$key] ?? $default;
    }

    public function has(string $key): bool
    {
        return isset($this->value[$key]);
    }

    public function with(string $key, mixed $value): self
    {
        $data = $this->value;
        $data[$key] = $value;
        return new self($data);
    }

    public function toArray(): array
    {
        return $this->value;
    }

    public function toString(): string
    {
        return json_encode($this->value);
    }
}
