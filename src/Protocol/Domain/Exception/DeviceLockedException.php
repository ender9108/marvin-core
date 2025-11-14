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

namespace Marvin\Protocol\Domain\Exception;

use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use EnderLab\DddCqrsBundle\Domain\Exception\Interfaces\TranslatableExceptionInterface;
use Marvin\Shared\Domain\ValueObject\Identity\DeviceId;

class DeviceLockedException extends DomainException implements TranslatableExceptionInterface
{
    public function __construct(
        string $message,
        public readonly ?string $deviceId = null,
    ) {
        parent::__construct($message);
    }

    public static function withDevice(DeviceId $deviceId): self
    {
        return new self(
            sprintf('Device %s is currently locked by another command', $deviceId->toString()),
            $deviceId->toString(),
        );
    }

    public function translationId(): string
    {
        if (null !== $this->deviceId) {
            return 'protocol.exceptions.PR0008.device_locked_with_id';
        }

        return 'protocol.exceptions.PR0007.device_locked';
    }

    public function translationParameters(): array
    {
        return [
            '%device_id%' => $this->deviceId,
        ];
    }

    public function translationDomain(): string
    {
        return 'protocol';
    }
}
