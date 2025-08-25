<?php

namespace App\Domotic\Domain\Repository;

use App\Domotic\Domain\Model\Protocol;
use EnderLab\DddCqrsBundle\Domain\Repository\RepositoryInterface;

interface ProtocolRepositoryInterface extends RepositoryInterface
{
    public function add(Protocol $protocol): void;

    public function remove(Protocol $protocol): void;

    public function byId(string $id): ?Protocol;

    public function isEnabled(string $reference): bool;

    public function getByReference(string $reference): ?Protocol;
}
