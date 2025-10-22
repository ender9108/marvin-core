<?php

namespace Marvin\Device\Application\Service\Acl;

use Marvin\Shared\Domain\ValueObject\Identity\ProtocolId;

interface ProtocolQueryServiceInterface
{
    public function getProtocolInfo(ProtocolId $protocolId): ?ProtocolInfo;

    public function isProtocolEnabled(ProtocolId $protocolId): bool;

    public function protocolExists(ProtocolId $protocolId): bool;

    public function getProtocolType(ProtocolId $protocolId): ?string;
}
