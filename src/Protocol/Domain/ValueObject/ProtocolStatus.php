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

namespace Marvin\Protocol\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\ValueObject\ValueObjectTrait;
use EnderLab\ToolsBundle\Service\EnumToArrayTrait;

enum ProtocolStatus: string
{
    use EnumToArrayTrait;
    use ValueObjectTrait;

    case CONNECTED = 'connected';
    case DISCONNECTED = 'disconnected';
    case ERROR = 'error';

    public function isConnected(): bool
    {
        return $this === self::CONNECTED;
    }

    public function isDisconnected(): bool
    {
        return $this === self::DISCONNECTED;
    }

    public function isError(): bool
    {
        return $this === self::ERROR;
    }
}
