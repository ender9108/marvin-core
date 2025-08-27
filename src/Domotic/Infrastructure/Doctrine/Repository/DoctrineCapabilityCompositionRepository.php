<?php

namespace App\Domotic\Infrastructure\Doctrine\Repository;

use App\Domotic\Domain\Model\CapabilityComposition;
use App\Domotic\Domain\Repository\CapabilityCompositionRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class DoctrineCapabilityCompositionRepository extends ServiceEntityRepository implements CapabilityCompositionRepositoryInterface
{
    private const string ENTITY_CLASS = CapabilityComposition::class;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, self::ENTITY_CLASS);
    }

    public function add(CapabilityComposition $capabilityComposition): void
    {
        $this->getEntityManager()->persist($capabilityComposition);
    }

    public function remove(CapabilityComposition $capabilityComposition): void
    {
        $this->getEntityManager()->remove($capabilityComposition);
    }

    public function byId(string|int $id): ?CapabilityComposition
    {
        return $this->find($id);
    }
}
