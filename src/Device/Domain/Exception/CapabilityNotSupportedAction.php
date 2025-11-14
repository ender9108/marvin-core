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
use Marvin\Device\Domain\ValueObject\CapabilityAction;
use Marvin\Shared\Domain\ValueObject\Identity\DeviceId;

/**
 * Exception levée quand un device ne supporte pas une action spécifique pour une capability
 */
final class CapabilityNotSupportedAction extends DomainException implements TranslatableExceptionInterface
{
    public function __construct(
        string $message,
        private readonly ?string $deviceId = null,
        private readonly ?string $action = null,
        private readonly ?string $capability = null,
    ) {
        parent::__construct($message);
    }

    public static function forDevice(
        DeviceId $deviceId,
        Capability $capability,
        CapabilityAction $action
    ): self {
        return new self(
            sprintf(
                'Device %s does not support action "%s" for capability "%s"',
                $deviceId->toString(),
                $action->value,
                $capability->value
            ),
            $deviceId->toString(),
            $action->value,
            $capability->value
        );
    }

    public function translationId(): string
    {
        if (null !== $this->deviceId && null !== $this->action && null !== $this->capability) {
            return 'device.exceptions.DE0027.capability_not_supported_action_with_capability_name_and_action';
        }

        return 'device.exceptions.DE0026.capability_not_supported_action';
    }

    public function translationParameters(): array
    {
        return [
            '%device_id%' => $this->deviceId,
            '%capability%' => $this->capability,
            '%action%' => $this->action,
        ];
    }

    public function translationDomain(): string
    {
        return 'device';
    }
}
