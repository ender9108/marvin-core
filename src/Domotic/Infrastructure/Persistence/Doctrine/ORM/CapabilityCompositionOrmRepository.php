<?php

namespace Marvin\Domotic\Infrastructure\Persistence\Doctrine\ORM;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Marvin\Domotic\Domain\Exception\CapabilityCompositionNotFound;
use Marvin\Domotic\Domain\Model\CapabilityComposition;
use Marvin\Domotic\Domain\Repository\CapabilityCompositionRepositoryInterface;
use Marvin\Domotic\Domain\ValueObject\Identity\CapabilityCompositionId;
use Override;

/**
 * @extends ServiceEntityRepository<CapabilityComposition>
 */
final class CapabilityCompositionOrmRepository extends ServiceEntityRepository implements CapabilityCompositionRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CapabilityComposition::class);
    }

    #[Override]
    public function save(CapabilityComposition $model, bool $flush = true): void
    {
        $this->getEntityManager()->persist($model);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    #[Override]
    public function remove(CapabilityComposition $model, bool $flush = true): void
    {
        $this->getEntityManager()->remove($model);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    #[Override]
    public function byId(CapabilityCompositionId $id): CapabilityComposition
    {
        $entity = $this->findOneBy(['id' => $id]);
        if (null === $entity) {
            throw CapabilityCompositionNotFound::withId($id);
        }
        return $entity;
    }
}
