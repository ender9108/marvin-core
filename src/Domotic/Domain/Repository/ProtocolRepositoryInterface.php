<?php

namespace Marvin\Domotic\Domain\Repository;

use Marvin\Domotic\Domain\Model\Protocol;
use Marvin\Domotic\Domain\ValueObject\Identity\ProtocolId;
use Marvin\Shared\Domain\ValueObject\Reference;

interface ProtocolRepositoryInterface
{
    public function save(Protocol $model, bool $flush = true): void;

    public function remove(Protocol $model, bool $flush = true): void;

    public function byId(ProtocolId $id): Protocol;

    public function byReference(Reference $reference): Protocol;
}
