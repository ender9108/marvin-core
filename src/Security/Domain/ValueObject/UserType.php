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

enum UserType: string
{
    use ValueObjectTrait;
    use EnumToArrayTrait;

    case APP = 'app';
    case CLI = 'cli';

    public static function translations(): array
    {
        return [
            self::APP->value => 'security.user.type.app',
            self::CLI->value => 'security.user.type.cli',
        ];
    }
}
