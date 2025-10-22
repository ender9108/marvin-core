<?php

namespace Marvin\Protocol\Domain\Repository;


use Marvin\Protocol\Domain\Model\Protocol;
use Marvin\Shared\Domain\ValueObject\Identity\ProtocolId;

interface ProtocolRepositoryInterface
{
    public function save(Protocol $protocol): void;

    public function remove(Protocol $protocol): void;

    public function byId(ProtocolId $id): Protocol;
}
