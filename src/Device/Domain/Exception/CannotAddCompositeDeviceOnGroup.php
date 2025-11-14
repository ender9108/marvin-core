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
use Marvin\Shared\Domain\ValueObject\Identity\DeviceId;

final class CannotAddCompositeDeviceOnGroup extends DomainException implements TranslatableExceptionInterface
{
    public function __construct(
        string $message,
        private readonly ?string $deviceId = null,
    ) {
        parent::__construct($message);
    }

    public static function withDeviceId(DeviceId $deviceId): self
    {
        return new self(
            sprintf(
                'Cannot add composite device %s to a group',
                $deviceId->toString()
            ),
            $deviceId->toString()
        );
    }

    public function translationId(): string
    {
        if (null !== $this->deviceId) {
            return 'device.exceptions.DE0047.cannot_add_composite_on_group_with_device_id';
        }

        return 'device.exceptions.DE0046.cannot_add_composite_on_group';
    }

    public function translationParameters(): array
    {
        return [
            '%device_id%' => $this->deviceId,
        ];
    }

    public function translationDomain(): string
    {
        return 'device';
    }
}
