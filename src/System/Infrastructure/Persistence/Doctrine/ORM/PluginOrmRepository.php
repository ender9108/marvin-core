<?php

namespace Marvin\System\Infrastructure\Persistence\Doctrine\ORM;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use EnderLab\DddCqrsBundle\Infrastructure\Persistence\Doctrine\ORM\PaginatorOrm;
use Marvin\Shared\Domain\ValueObject\Reference;
use Marvin\System\Domain\Exception\PluginNotFound;
use Marvin\System\Domain\List\PluginStatusReference;
use Marvin\System\Domain\Model\Plugin;
use Marvin\System\Domain\Repository\PluginRepositoryInterface;
use Marvin\System\Domain\ValueObject\Identity\PluginId;
use Marvin\System\Infrastructure\Persistence\Doctrine\Cache\SystemCacheKeys;
use Override;

/**
 * @extends ServiceEntityRepository<Plugin>
 */
final class PluginOrmRepository extends ServiceEntityRepository implements PluginRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Plugin::class);
    }

    #[Override]
    public function save(Plugin $model, bool $flush = true): void
    {
        $this->getEntityManager()->persist($model);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    #[Override]
    public function remove(Plugin $model, bool $flush = true): void
    {
        $this->getEntityManager()->remove($model);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    #[Override]
    public function byId(PluginId $id): Plugin
    {
        $entity = $this->findOneBy(['id' => $id]);
        if (null === $entity) {
            throw PluginNotFound::withId($id);
        }
        return $entity;
    }

    #[Override]
    public function exists(Reference $reference): bool
    {
        $count = $this
            ->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->innerJoin('p.status', 's')
            ->andWhere('p.reference = :reference')
            ->setParameter('reference', $reference->value)
            ->andWhere('s.reference = :status')
            ->setParameter('status', PluginStatusReference::STATUS_ENABLED->value)
            ->getQuery()
            ->getSingleScalarResult()
        ;

        return $count === 1;
    }

    #[Override]
    public function getByReference(Reference $reference): ?Plugin
    {
        return $this
            ->createQueryBuilder('p')
            ->andWhere('p.reference = :reference')
            ->setParameter('reference', $reference->value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function collection(array $filters = [], array $orderBy = [], int $page = 0, int $itemsPerPage = 50): PaginatorOrm
    {
        $query = $this
            ->createQueryBuilder('p')
            ->setCacheable(true)
            ->setCacheMode(ClassMetadata::CACHE_USAGE_NONSTRICT_READ_WRITE)
            ->setCacheRegion(SystemCacheKeys::PLUGIN_LIST->value)
        ;

        if (!empty($filters)) {
            foreach ($filters as $field => $value) {
                switch ($field) {
                    case 'label':
                        $query
                            ->andWhere('p.label LIKE :label')
                            ->setParameter('label', '%'.$value.'%')
                        ;
                        break;
                    case 'reference':
                        $query
                            ->andWhere('p.reference = :reference')
                            ->setParameter('reference', $value)
                        ;
                        break;
                    case 'version':
                        $query
                            ->andWhere('p.version = :version')
                            ->setParameter('version', $value)
                        ;
                        break;
                    case 'status':
                        $query
                            ->andWhere('p.status = :status')
                            ->setParameter('status', $value)
                        ;
                        break;
                }
            }
        }

        if (!empty($orderBy)) {
            foreach ($orderBy as $field => $direction) {
                $query->addOrderBy('p.'.$field, $direction);
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
