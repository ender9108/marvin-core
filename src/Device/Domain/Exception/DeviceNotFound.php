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

use EnderLab\DddCqrsApiPlatformBundle\Domain\Exception\NotFoundInterface;
use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use EnderLab\DddCqrsBundle\Domain\Exception\Interfaces\TranslatableExceptionInterface;
use Marvin\Shared\Domain\ValueObject\Identity\DeviceId;

class DeviceNotFound extends DomainException implements TranslatableExceptionInterface, NotFoundInterface
{
    public function __construct(
        string $message,
        public readonly ?string $id = null,
    ) {
        parent::__construct($message);
    }

    public static function withId(DeviceId $id): self
    {
        return new self(
            sprintf('The device %s is not found', $id->toString()),
            $id->toString(),
        );
    }

    public function translationId(): string
    {
        if (null !== $this->id) {
            return 'device.exceptions.DE0002.device_not_found_with_id';
        }

        return 'device.exceptions.DE0001.device_not_found';
    }

    public function translationParameters(): array
    {
        return [
            '%id%' => $this->id
        ];
    }

    public function translationDomain(): string
    {
        return 'device';
    }
}
