<?php

namespace Marvin\Domotic\Infrastructure\Persistence\Doctrine\ORM;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Marvin\Domotic\Domain\Exception\CapabilityNotFound;
use Marvin\Domotic\Domain\Model\Capability;
use Marvin\Domotic\Domain\Repository\CapabilityRepositoryInterface;
use Marvin\Domotic\Domain\ValueObject\Identity\CapabilityId;
use Override;

/**
 * @extends ServiceEntityRepository<Capability>
 */
final class CapabilityOrmRepository extends ServiceEntityRepository implements CapabilityRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Capability::class);
    }

    #[Override]
    public function save(Capability $model, bool $flush = true): void
    {
        $this->getEntityManager()->persist($model);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    #[Override]
    public function remove(Capability $model, bool $flush = true): void
    {
        $this->getEntityManager()->remove($model);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    #[Override]
    public function byId(CapabilityId $id): Capability
    {
        $entity = $this->findOneBy(['id' => $id]);
        if (null === $entity) {
            throw CapabilityNotFound::withId($id);
        }
        return $entity;
    }
}
