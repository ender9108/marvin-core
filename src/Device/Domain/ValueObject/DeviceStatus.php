<?php

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
