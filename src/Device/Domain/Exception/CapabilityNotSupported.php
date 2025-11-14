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

namespace Marvin\Device\Domain\Exception;

use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use EnderLab\DddCqrsBundle\Domain\Exception\Interfaces\TranslatableExceptionInterface;
use Marvin\Device\Domain\ValueObject\Capability;
use Marvin\Shared\Domain\ValueObject\Identity\DeviceId;

final class CapabilityNotSupported extends DomainException implements TranslatableExceptionInterface
{
    public function __construct(
        string $message,
        private readonly ?string $deviceId = null,
        private readonly ?string $capability = null,
        private readonly ?string $protocol = null,
    ) {
        parent::__construct($message);
    }

    public static function forDevice(DeviceId $deviceId, Capability $capability): self
    {
        return new self(
            sprintf(
                'Device %s does not support capability: %s',
                $deviceId->toString(),
                $capability->value
            ),
            $deviceId->toString(),
            $capability->value,
        );
    }

    public static function byProtocol(string $protocol, Capability $capability): self
    {
        return new self(
            sprintf(
                'Protocol %s does not support capability: %s',
                $protocol,
                $capability->value
            ),
            null,
            $capability->value,
            $protocol,
        );
    }

    public function translationId(): string
    {
        if (null !== $this->deviceId && null !== $this->capability) {
            return 'device.exceptions.DE0044.capability_not_supported_by_device';
        }

        if (null !== $this->protocol && null !== $this->capability) {
            return 'device.exceptions.DE0045.capability_not_supported_by_protocol';
        }
    }

    public function translationParameters(): array
    {
        return [
            '%device_id%' => $this->deviceId,
            '%capability%' => $this->capability,
            '%protocol%' => $this->protocol,
        ];
    }

    public function translationDomain(): string
    {
        return 'device';
    }
}
