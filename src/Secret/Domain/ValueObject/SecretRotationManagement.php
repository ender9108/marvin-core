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

enum SecretRotationManagement: string
{
    /**
     * Secret géré par Marvin (peut être auto-généré et auto-tourné)
     * Ex: MQTT password pour ESP, tokens internes
     */
    case MANAGED = 'managed';

    /**
     * Secret externe fourni par l'user (rotation manuelle uniquement)
     * Ex: API keys externes, WiFi password
     */
    case EXTERNAL = 'external';

    public function isManaged(): bool
    {
        return $this === self::MANAGED;
    }

    public function isExternal(): bool
    {
        return $this === self::EXTERNAL;
    }

    public function canAutoRotate(): bool
    {
        return $this->isManaged();
    }
}
