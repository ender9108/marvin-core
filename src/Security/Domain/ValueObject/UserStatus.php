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

namespace Marvin\Security\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\ValueObject\ValueObjectTrait;
use EnderLab\ToolsBundle\Service\EnumToArrayTrait;

enum UserStatus: string
{
    use ValueObjectTrait;
    use EnumToArrayTrait;

    case DISABLED = 'disabled';
    case ENABLED = 'enabled';
    case LOCKED = 'locked';
    case TO_DELETE = 'to_delete';

    public function isDisabled(): bool
    {
        return $this === self::DISABLED;
    }

    public function isEnabled(): bool
    {
        return $this === self::ENABLED;
    }

    public function isLocked(): bool
    {
        return $this === self::LOCKED;
    }

    public function isToDelete(): bool
    {
        return $this === self::TO_DELETE;
    }

    public static function disabled(): self
    {
        return self::DISABLED;
    }

    public static function enabled(): self
    {
        return self::ENABLED;
    }

    public static function locked(): self
    {
        return self::LOCKED;
    }

    public static function toDelete(): self
    {
        return self::TO_DELETE;
    }

    public static function translations(): array
    {
        return [
            self::DISABLED->value => 'security.user.status.disabled',
            self::ENABLED->value => 'security.user.status.enabled',
            self::LOCKED->value => 'security.user.status.locked',
            self::TO_DELETE->value => 'security.user.status.to_delete',
        ];
    }
}
