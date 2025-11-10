<?php

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
