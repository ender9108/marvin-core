<?php

namespace Marvin\Location\Infrastructure\Persistence\Doctrine\ORM;

use Marvin\Location\Domain\ValueObject\ZoneType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use EnderLab\DddCqrsBundle\Domain\Repository\PaginatorInterface;
use EnderLab\DddCqrsBundle\Infrastructure\Persistence\Doctrine\ORM\PaginatorOrm;
use Marvin\Location\Domain\Exception\ZoneNotFound;
use Marvin\Location\Domain\Model\Zone;
use Marvin\Location\Domain\Repository\ZoneRepositoryInterface;
use Marvin\Location\Infrastructure\Persistence\Doctrine\Cache\LocationCacheKeys;
use Marvin\Shared\Domain\ValueObject\Identity\ZoneId;
use Marvin\Shared\Domain\ValueObject\Label;

class ZoneOrmRepository extends ServiceEntityRepository implements ZoneRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Zone::class);
    }

    public function save(Zone $zone): void
    {
        $this->getEntityManager()->persist($zone);
        $this->getEntityManager()->flush();
    }

    public function remove(Zone $zone): void
    {
        $this->getEntityManager()->remove($zone);
        $this->getEntityManager()->flush();
    }

    #[Override]
    public function byId(ZoneId $id): Zone
    {
        /** @var Zone $zone */
        $zone = $this->findOneBy(['id' => $id]);

        if (null === $zone) {
            throw ZoneNotFound::withId($id);
        }

        return $zone;
    }

    public function byLabel(Label $label): Zone
    {
        return $this
            ->createQueryBuilder('z')
            ->where('z.name.value = :name')
            ->setParameter('name', $label)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function all(): array
    {
        return $this->createQueryBuilder('z')
            ->orderBy('z.path.value', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function byType(ZoneType $type): array
    {
        return $this->createQueryBuilder('z')
            ->where('z.type = :type')
            ->setParameter('type', $type)
            ->orderBy('z.name.value', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function byParentZoneId(?ZoneId $parentZoneId): array
    {
        $qb = $this->createQueryBuilder('z');

        if ($parentZoneId === null) {
            $qb->where('z.parentZoneId IS NULL');
        } else {
            $qb
                ->where('z.parentZoneId = :parentZoneId')
                ->setParameter('parentZoneId', $parentZoneId)
            ;
        }

        return $qb->orderBy('z.name.value', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function getRootZones(): array
    {
        return $this->byParentZoneId(null);
    }

    public function getHierarchy(): array
    {
        return $this->all();
    }

    public function countChildren(ZoneId $zoneId): int
    {
        return (int) $this->createQueryBuilder('z')
            ->select('COUNT(z.id)')
            ->where('z.parentZoneId = :zoneId')
            ->setParameter('zoneId', $zoneId)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function hasChildren(ZoneId $zoneId): bool
    {
        return $this->countChildren($zoneId) > 0;
    }

    public function getDescendants(ZoneId $zoneId): array
    {
        $zone = $this->find($zoneId->toString());
        if ($zone === null) {
            return [];
        }

        $pathPrefix = $zone->getPath()->toString();

        return $this->createQueryBuilder('z')
            ->where('z.path.value LIKE :pathPrefix')
            ->andWhere('z.id != :zoneId')
            ->setParameter('pathPrefix', $pathPrefix . '/%')
            ->setParameter('zoneId', $zoneId)
            ->orderBy('z.path.value', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function collection(array $filters = [], array $orderBy = [], int $page = 0, int $itemsPerPage = 50): PaginatorInterface
    {
        $query = $this
            ->createQueryBuilder('z')
            ->setCacheable(true)
            ->setCacheMode(ClassMetadata::CACHE_USAGE_NONSTRICT_READ_WRITE)
            ->setCacheRegion(LocationCacheKeys::ZONE_LIST->value)
        ;

        /*if (!empty($filters)) {
            foreach ($filters as $field => $value) {
                switch ($field) {

                }
            }
        }*/

        if (!empty($orderBy)) {
            foreach ($orderBy as $field => $direction) {
                $query->addOrderBy('z.'.$field, $direction);
            }
        } else {
            $query->orderBy('z.name', 'ASC');
        }

        $query
            ->setFirstResult(($page - 1) * $itemsPerPage)
            ->setMaxResults($itemsPerPage)
        ;

        $paginator = new Paginator($query->getQuery());

        return new PaginatorOrm($paginator);
    }
}
