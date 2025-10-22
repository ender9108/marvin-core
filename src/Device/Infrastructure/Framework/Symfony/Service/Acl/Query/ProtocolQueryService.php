<?php

namespace Marvin\Device\Infrastructure\Framework\Symfony\Service\Acl\Query;

use Marvin\Device\Application\Service\Acl\ProtocolInfo;
use Marvin\Device\Application\Service\Acl\ProtocolQueryServiceInterface;
use Marvin\Protocol\Domain\Repository\ProtocolRepositoryInterface;
use Marvin\Shared\Domain\ValueObject\Identity\ProtocolId;

final class ProtocolQueryService implements ProtocolQueryServiceInterface
{
    private array $localCache = [];

    public function __construct(
        private readonly ProtocolRepositoryInterface $protocolRepository
    ) {}

    public function getProtocolInfo(ProtocolId $protocolId): ?ProtocolInfo
    {
        $protocol = $this->protocolRepository->byId($protocolId);

        if (false === isset($this->localCache[$protocol->id->toString()])) {
            $this->localCache[$protocol->id->toString()] = new ProtocolInfo(
                id: $protocol->id,
                label: $protocol->label,
                type: $protocol->type,
                isEnabled: $protocol->isEnabled,
                status: $protocol->status ?? null,
                metadata: $protocol->metadata?->toArray()
            );
        }

        return $this->localCache[$protocol->id->toString()];
    }

    public function isProtocolEnabled(ProtocolId $protocolId): bool
    {
        $info = $this->getProtocolInfo($protocolId);
        return $info?->isEnabled ?? false;
    }

    public function protocolExists(ProtocolId $protocolId): bool
    {
        return $this->protocolRepository->byId($protocolId) !== null;
    }

    public function getProtocolType(ProtocolId $protocolId): ?string
    {
        $info = $this->getProtocolInfo($protocolId);
        return $info?->type;
    }
}

