<?php

/**
 * Marvin Core - DDD-based home automation system
 *
 * @package   Marvin\Core
 * @author    Alexandre Berthelot <alexandreberthelot9108@gmail.com>
 * @copyright 2024-present Alexandre Berthelot
 * @license   AGPL-3.0 License
 * @link      https://github.com/ender9108/marvin-core
 */

declare(strict_types=1);

namespace Marvin\System\Infrastructure\Persistence\Doctrine\ORM;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use EnderLab\DddCqrsBundle\Domain\Repository\PaginatorInterface;
use EnderLab\DddCqrsBundle\Infrastructure\Persistence\Doctrine\ORM\PaginatorOrm;
use Marvin\System\Domain\Exception\ContainerNotFound;
use Marvin\System\Domain\Model\Container;
use Marvin\System\Domain\Repository\ContainerRepositoryInterface;
use Marvin\System\Domain\ValueObject\Identity\ContainerId;
use Marvin\System\Infrastructure\Persistence\Doctrine\Cache\SystemCacheKeys;
use Override;

/**
 * @extends ServiceEntityRepository<Container>
 */
final class ContainerOrmRepository extends ServiceEntityRepository implements ContainerRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Container::class);
    }

    #[Override]
    public function save(Container $model, bool $flush = true): void
    {
        $this->getEntityManager()->persist($model);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    #[Override]
    public function remove(Container $model, bool $flush = true): void
    {
        $this->getEntityManager()->remove($model);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    #[Override]
    public function byId(ContainerId $id): Container
    {
        $entity = $this->findOneBy(['id' => $id]);

        if (null === $entity) {
            throw ContainerNotFound::withId($id);
        }

        return $entity;
    }

    public function collection(array $filters = [], array $orderBy = [], int $page = 0, int $itemsPerPage = 50): PaginatorInterface
    {
        $query = $this
            ->createQueryBuilder('c')
            ->setCacheable(true)
            ->setCacheMode(ClassMetadata::CACHE_USAGE_NONSTRICT_READ_WRITE)
            ->setCacheRegion(SystemCacheKeys::CONTAINER_LIST->value)
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
                    case 'type':
                        $query
                            ->andWhere('c.type = :type')
                            ->setParameter('type', $value)
                        ;
                        break;
                    case 'status':
                        $query
                            ->andWhere('c.status = :status')
                            ->setParameter('status', $value)
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
