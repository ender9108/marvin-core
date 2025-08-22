<?php

namespace App\Domotic\Infrastructure\Doctrine\Repository;

use App\Domotic\Domain\Model\Capability;
use App\Domotic\Domain\Repository\CapabilityRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class DoctrineCapabilityRepository extends ServiceEntityRepository implements CapabilityRepositoryInterface
{
    private const string ENTITY_CLASS = Capability::class;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, self::ENTITY_CLASS);
    }

    public function add(Capability $capability): void
    {
        $this->getEntityManager()->persist($capability);
    }

    public function remove(Capability $capability): void
    {
        $this->getEntityManager()->remove($capability);
    }

    public function byId(string $id): ?Capability
    {
        return $this->find($id);
    }
}
