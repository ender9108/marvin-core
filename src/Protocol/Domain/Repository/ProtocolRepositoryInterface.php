<?php

namespace Marvin\Protocol\Domain\Repository;

use Marvin\Protocol\Domain\Model\Protocol;
use Marvin\Protocol\Domain\ValueObject\ProtocolStatus;
use Marvin\Protocol\Domain\ValueObject\TransportType;
use Marvin\Shared\Domain\ValueObject\Identity\ProtocolId;
use Marvin\Shared\Domain\ValueObject\Label;

interface ProtocolRepositoryInterface
{
    public function save(Protocol $protocol, bool $flush = true): void;

    public function remove(Protocol $protocol, bool $flush = true): void;

    /**
     * @return Protocol[]
     */
    public function all(): array;

    public function byId(ProtocolId $id): Protocol;

    public function byName(Label $name): ?Protocol;

    public function byTransportType(TransportType $type): array;

    public function byStatus(ProtocolStatus $status): array;

    public function exists(ProtocolId $id): bool;
}
