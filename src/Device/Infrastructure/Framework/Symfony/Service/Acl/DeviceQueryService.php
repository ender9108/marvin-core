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

namespace Marvin\Device\Infrastructure\Framework\Symfony\Service\Acl;

use Marvin\Device\Domain\Exception\DeviceNotFound;
use Marvin\Device\Domain\Repository\DeviceRepositoryInterface;
use Marvin\Shared\Application\Service\Acl\DeviceQueryServiceInterface;
use Marvin\Shared\Application\Service\Acl\Dto\DeviceDto;
use Marvin\Shared\Domain\ValueObject\Identity\DeviceId;
use Psr\Log\LoggerInterface;
use Throwable;

/**
 * ACL Service for querying Device Context from Protocol Context
 */
final readonly class DeviceQueryService implements DeviceQueryServiceInterface
{
    public function __construct(
        private DeviceRepositoryInterface $deviceRepository,
        private LoggerInterface $logger,
    ) {
    }

    public function getDevice(string $deviceId): DeviceDto
    {
        try {
            $deviceId = DeviceId::fromString($deviceId);
            $device = $this->deviceRepository->byId($deviceId);

            // Build DeviceDTO from Device aggregate
            return new DeviceDto(
                id: $device->id->toString(),
                label: $device->label->value,
                protocol: $device->protocol->value,
                nativeId: $device->physicalAddress?->value ?? $device->id->toString(),
                mqttTopic: $device->metadata?->get('mqtt_topic'),
                restUrl: $device->metadata?->get('rest_url'),
                metadata: $device->metadata?->toArray() ?? [],
            );
        } catch (Throwable $e) {
            $this->logger->error('Error retrieving device from Device Context', [
                'deviceId' => $deviceId,
                'error' => $e->getMessage(),
            ]);

            throw DeviceNotFound::withId($deviceId);
        }
    }

    public function getDeviceNativeId(string $deviceId): string
    {
        $device = $this->getDevice($deviceId);

        return $device->nativeId;
    }

    public function getDeviceProtocol(string $deviceId): string
    {
        $device = $this->getDevice($deviceId);

        return $device->protocol;
    }

    public function getDeviceMqttTopic(string $deviceId): string
    {
        $device = $this->getDevice($deviceId);

        return $device->mqttTopic;
    }

    public function getDeviceRestUrl(string $deviceId): string
    {
        $device = $this->getDevice($deviceId);

        return $device->restUrl;
    }

    public function getDeviceMetadata(string $deviceId): array
    {
        $device = $this->getDevice($deviceId);

        return $device->metadata;
    }
}
