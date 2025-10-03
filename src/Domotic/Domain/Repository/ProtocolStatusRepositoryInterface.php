<?php

namespace Marvin\Domotic\Domain\Repository;

use Marvin\Domotic\Domain\Model\ProtocolStatus;
use Marvin\Domotic\Domain\ValueObject\Identity\ProtocolStatusId;

interface ProtocolStatusRepositoryInterface
{
    public function save(ProtocolStatus $model, bool $flush = true): void;

    public function remove(ProtocolStatus $model, bool $flush = true): void;

    public function byId(ProtocolStatusId $id): ProtocolStatus;
}
