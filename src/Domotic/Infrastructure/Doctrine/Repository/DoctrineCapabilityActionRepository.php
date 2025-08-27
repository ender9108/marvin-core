<?php

namespace App\Domotic\Infrastructure\Doctrine\Repository;

use App\Domotic\Domain\Model\CapabilityAction;
use App\Domotic\Domain\Repository\CapabilityActionRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class DoctrineCapabilityActionRepository extends ServiceEntityRepository implements CapabilityActionRepositoryInterface
{
    private const string ENTITY_CLASS = CapabilityAction::class;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, self::ENTITY_CLASS);
    }

    public function add(CapabilityAction $capabilityAction): void
    {
        $this->getEntityManager()->persist($capabilityAction);
    }

    public function remove(CapabilityAction $capabilityAction): void
    {
        $this->getEntityManager()->remove($capabilityAction);
    }

    public function byId(string $id): ?CapabilityAction
    {
        return $this->find($id);
    }
}
