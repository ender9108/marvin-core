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

namespace Marvin\Device\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\ValueObject\ValueObjectTrait;
use EnderLab\ToolsBundle\Service\EnumToArrayTrait;

/**
 * DeviceStatus - Statut de disponibilité du device
 *
 * Indique si le device est joignable et opérationnel
 */
enum DeviceStatus: string
{
    use EnumToArrayTrait;
    use ValueObjectTrait;

    /**
     * ONLINE - Device joignable et opérationnel
     */
    case ONLINE = 'online';

    /**
     * OFFLINE - Device injoignable ou déconnecté
     */
    case OFFLINE = 'offline';

    /**
     * UNKNOWN - Statut inconnu (jamais vu ou timeout)
     */
    case UNKNOWN = 'unknown';
}
