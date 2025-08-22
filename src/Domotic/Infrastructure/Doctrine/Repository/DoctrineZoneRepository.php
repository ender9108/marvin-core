<?php

namespace App\Domotic\Infrastructure\Doctrine\Repository;

use App\Domotic\Domain\Model\Zone;
use App\Domotic\Domain\Repository\ZoneRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class DoctrineZoneRepository extends ServiceEntityRepository implements ZoneRepositoryInterface
{
    private const string ENTITY_CLASS = Zone::class;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, self::ENTITY_CLASS);
    }

    public function add(Zone $zone): void
    {
        $this->getEntityManager()->persist($zone);
    }

    public function remove(Zone $zone): void
    {
        $this->getEntityManager()->remove($zone);
    }

    public function byId(int $id): ?Zone
    {
        return $this->find($id);
    }
}
