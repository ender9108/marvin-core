<?php

namespace Marvin\System\Infrastructure\Persistence\Doctrine\ORM;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use EnderLab\DddCqrsBundle\Domain\Repository\PaginatorInterface;
use EnderLab\DddCqrsBundle\Infrastructure\Persistence\Doctrine\ORM\PaginatorOrm;
use Marvin\System\Domain\Exception\WorkerNotFound;
use Marvin\System\Domain\Model\Worker;
use Marvin\System\Domain\Repository\WorkerRepositoryInterface;
use Marvin\System\Domain\ValueObject\Identity\WorkerId;
use Marvin\System\Infrastructure\Persistence\Doctrine\Cache\SystemCacheKeys;
use Override;

/**
 * @extends ServiceEntityRepository<Worker>
 */
final class WorkerOrmRepository extends ServiceEntityRepository implements WorkerRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Worker::class);
    }

    #[Override]
    public function save(Worker $model, bool $flush = true): void
    {
        $this->getEntityManager()->persist($model);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    #[Override]
    public function remove(Worker $model, bool $flush = true): void
    {
        $this->getEntityManager()->remove($model);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    #[Override]
    public function byId(WorkerId $id): Worker
    {
        $entity = $this->findOneBy(['id' => $id]);
        if (null === $entity) {
            throw WorkerNotFound::withId($id);
        }
        return $entity;
    }

    #[Override]
    public function collection(array $filters = [], array $orderBy = [], int $page = 0, int $itemsPerPage = 50): PaginatorInterface
    {
        $query = $this
            ->createQueryBuilder('w')
            ->setCacheable(true)
            ->setCacheMode(ClassMetadata::CACHE_USAGE_NONSTRICT_READ_WRITE)
            ->setCacheRegion(SystemCacheKeys::WORKER_LIST->value)
        ;

        if (!empty($filters)) {
            foreach ($filters as $field => $value) {
                switch ($field) {
                    case 'type':
                        $query
                            ->andWhere('w.type = :type')
                            ->setParameter('type', $value)
                        ;
                        break;
                    case 'status':
                        $query
                            ->andWhere('w.status = :status')
                            ->setParameter('status', $value)
                        ;
                        break;
                    case 'label':
                        $query
                            ->andWhere('w.label.value LIKE :label')
                            ->setParameter('label', '%'.$value.'%')
                        ;
                        break;
                }
            }
        }

        if (!empty($orderBy)) {
            foreach ($orderBy as $field => $direction) {
                $query->addOrderBy('w.'.$field, $direction);
            }
        } else {
            $query->addOrderBy('w.id', 'ASC');
        }

        $query
            ->setFirstResult(($page - 1) * $itemsPerPage)
            ->setMaxResults($itemsPerPage)
        ;

        $paginator = new Paginator($query->getQuery());

        return new PaginatorOrm($paginator);
    }
}
