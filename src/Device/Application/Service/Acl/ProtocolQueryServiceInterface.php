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

namespace Marvin\Device\Application\Service\Acl;

use Marvin\Shared\Domain\ValueObject\Identity\ProtocolId;

/**
 * ACL: Device → Protocol (query)
 *
 * Service interface for querying protocol information from Device Context
 * Implemented in Infrastructure layer to avoid direct Protocol Context dependency
 */
interface ProtocolQueryServiceInterface
{
    /**
     * Check if a protocol exists
     */
    public function protocolExists(ProtocolId $protocolId): bool;

    /**
     * Check if a protocol is enabled and available
     */
    public function isProtocolEnabled(ProtocolId $protocolId): bool;

    /**
     * Get protocol status (ACTIVE, INACTIVE, ERROR)
     */
    public function getProtocolStatus(ProtocolId $protocolId): string;
}
