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

namespace Marvin\Secret\Domain\ValueObject;

use EnderLab\ToolsBundle\Service\EnumToArrayTrait;

enum SecretScope: string
{
    use EnumToArrayTrait;

    case GLOBAL = 'global';      // Accessible à toute l'app
    case USER = 'user';          // Spécifique à un user
    case DEVICE = 'device';      // Spécifique à un device
    case PROTOCOL = 'protocol';  // Spécifique à un protocole
}
