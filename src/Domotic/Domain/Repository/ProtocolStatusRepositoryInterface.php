<?php

namespace App\Domotic\Domain\Repository;

use App\Domotic\Domain\Model\ProtocolStatus;
use EnderLab\DddCqrsBundle\Domain\Repository\RepositoryInterface;

interface ProtocolStatusRepositoryInterface extends RepositoryInterface
{
    public function add(ProtocolStatus $protocolStatus): void;

    public function remove(ProtocolStatus $protocolStatus): void;

    public function byId(string $id): ?ProtocolStatus;
}
