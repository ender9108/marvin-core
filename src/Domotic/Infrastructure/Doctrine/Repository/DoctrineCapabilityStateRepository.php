<?php

namespace App\Domotic\Infrastructure\Doctrine\Repository;

use App\Domotic\Domain\Model\CapabilityState;
use App\Domotic\Domain\Repository\CapabilityStateRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class DoctrineCapabilityStateRepository extends ServiceEntityRepository implements CapabilityStateRepositoryInterface
{
    private const string ENTITY_CLASS = CapabilityState::class;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, self::ENTITY_CLASS);
    }

    public function add(CapabilityState $capabilityState): void
    {
        $this->getEntityManager()->persist($capabilityState);
    }

    public function remove(CapabilityState $capabilityState): void
    {
        $this->getEntityManager()->remove($capabilityState);
    }

    public function byId(string|int $id): ?CapabilityState
    {
        return $this->find($id);
    }
}
