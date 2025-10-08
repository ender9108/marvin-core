<?php

namespace Marvin\Domotic\Infrastructure\Persistence\Doctrine\ORM;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use EnderLab\DddCqrsBundle\Infrastructure\Persistence\Doctrine\ORM\PaginatorOrm;
use Marvin\Domotic\Domain\Exception\CapabilityNotFound;
use Marvin\Domotic\Domain\Model\Capability;
use Marvin\Domotic\Domain\Repository\CapabilityRepositoryInterface;
use Marvin\Domotic\Domain\ValueObject\Identity\CapabilityId;
use Marvin\Domotic\Infrastructure\Persistence\Doctrine\Cache\DomoticCacheKeys;
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

    public function collection(array $filters = [], array $orderBy = [], int $page = 0, int $itemsPerPage = 50): PaginatorOrm
    {
        $query = $this
            ->createQueryBuilder('c')
            ->setCacheable(true)
            ->setCacheMode(ClassMetadata::CACHE_USAGE_NONSTRICT_READ_WRITE)
            ->setCacheRegion(DomoticCacheKeys::CAPABILITY_LIST->value)
        ;

        if (!empty($filters)) {
            foreach ($filters as $field => $value) {
                switch ($field) {
                    case 'label':
                        $query
                            ->andWhere('c.label LIKE :label')
                            ->setParameter('label', '%'.$value.'%')
                        ;
                        break;
                    case 'reference':
                        $query
                            ->andWhere('c.reference = :reference')
                            ->setParameter('reference', $value)
                        ;
                        break;
                }
            }
        }

        if (!empty($orderBy)) {
            foreach ($orderBy as $field => $direction) {
                $query->addOrderBy('c.'.$field, $direction);
            }
        }

        $query
            ->setFirstResult(($page - 1) * $itemsPerPage)
            ->setMaxResults($itemsPerPage)
        ;

        $paginator = new Paginator($query->getQuery());

        return new PaginatorOrm($paginator);
    }
}
