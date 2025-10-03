<?php

namespace Marvin\Domotic\Infrastructure\Persistence\Doctrine\ORM;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Marvin\Domotic\Domain\Exception\CapabilityStateNotFound;
use Marvin\Domotic\Domain\Model\CapabilityState;
use Marvin\Domotic\Domain\Repository\CapabilityStateRepositoryInterface;
use Marvin\Domotic\Domain\ValueObject\Identity\CapabilityStateId;
use Override;

/**
 * @extends ServiceEntityRepository<CapabilityState>
 */
final class CapabilityStateOrmRepository extends ServiceEntityRepository implements CapabilityStateRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CapabilityState::class);
    }

    #[Override]
    public function save(CapabilityState $model, bool $flush = true): void
    {
        $this->getEntityManager()->persist($model);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    #[Override]
    public function remove(CapabilityState $model, bool $flush = true): void
    {
        $this->getEntityManager()->remove($model);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    #[Override]
    public function byId(CapabilityStateId $id): CapabilityState
    {
        $entity = $this->findOneBy(['id' => $id]);
        if (null === $entity) {
            throw CapabilityStateNotFound::withId($id);
        }
        return $entity;
    }
}
