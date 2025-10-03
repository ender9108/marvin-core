<?php

namespace Marvin\Domotic\Infrastructure\Persistence\Doctrine\ORM;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Marvin\Domotic\Domain\Exception\ZoneNotFound;
use Marvin\Domotic\Domain\Model\Zone;
use Marvin\Domotic\Domain\Repository\ZoneRepositoryInterface;
use Marvin\Domotic\Domain\ValueObject\Identity\ZoneId;
use Override;

/**
 * @extends ServiceEntityRepository<Zone>
 */
final class ZoneOrmRepository extends ServiceEntityRepository implements ZoneRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Zone::class);
    }

    #[Override]
    public function save(Zone $model, bool $flush = true): void
    {
        $this->getEntityManager()->persist($model);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    #[Override]
    public function remove(Zone $model, bool $flush = true): void
    {
        $this->getEntityManager()->remove($model);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    #[Override]
    public function byId(ZoneId $id): Zone
    {
        $entity = $this->findOneBy(['id' => $id]);
        if (null === $entity) {
            throw ZoneNotFound::withId($id);
        }
        return $entity;
    }
}
