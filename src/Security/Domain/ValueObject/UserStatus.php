<?php

namespace Marvin\Security\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use EnderLab\DddCqrsBundle\Domain\ValueObject\ValueObjectTrait;
use EnderLab\ToolsBundle\Service\EnumToArrayTrait;
use Stringable;

enum UserStatus: string {
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
}
