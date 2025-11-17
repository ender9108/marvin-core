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

use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use EnderLab\DddCqrsBundle\Domain\Repository\PaginatorInterface;
use EnderLab\DddCqrsBundle\Infrastructure\Persistence\Doctrine\ORM\PaginatorOrm;
use Marvin\Shared\Domain\ValueObject\Identity\UniqId;
use Marvin\System\Domain\Exception\ActionRequestNotFound;
use Marvin\System\Domain\Model\ActionRequest;
use Marvin\System\Domain\Repository\ActionRequestRepositoryInterface;
use Marvin\System\Domain\ValueObject\ActionStatus;
use Marvin\System\Domain\ValueObject\Identity\ActionRequestId;
use Marvin\System\Infrastructure\Persistence\Doctrine\Cache\SystemCacheKeys;
use Override;

/**
 * @extends ServiceEntityRepository<ActionRequest>
 */
final class ActionRequestOrmRepository extends ServiceEntityRepository implements ActionRequestRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ActionRequest::class);
    }

    #[Override]
    public function save(ActionRequest $model, bool $flush = true): void
    {
        $this->getEntityManager()->persist($model);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    #[Override]
    public function remove(ActionRequest $model, bool $flush = true): void
    {
        $this->getEntityManager()->remove($model);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    #[Override]
    public function byId(ActionRequestId $id): ActionRequest
    {
        $entity = $this->findOneBy(['id' => $id]);

        if (null === $entity) {
            throw ActionRequestNotFound::withId($id);
        }

        return $entity;
    }

    #[Override]
    public function byCorrelationId(UniqId $correlationId): ActionRequest
    {
        $entity = $this->findOneBy(['correlationId' => $correlationId]);

        if (null === $entity) {
            throw ActionRequestNotFound::withCorrelationId($correlationId);
        }

        return $entity;
    }

    #[Override]
    public function getPendingActions(): array
    {
        return $this->findBy(['status' => ActionStatus::PENDING->value], ['createdAt' => 'ASC']);
    }

    #[Override]
    public function getTimeoutActions(int $timeoutSeconds = 300, int $page = 0, int $itemsPerPage = 50): PaginatorInterface
    {
        $timeoutDate = new DateTimeImmutable("-{$timeoutSeconds} seconds");

        $query = $this
            ->createQueryBuilder('ar')
            ->setCacheable(true)
            ->setCacheMode(ClassMetadata::CACHE_USAGE_NONSTRICT_READ_WRITE)
            ->setCacheRegion(SystemCacheKeys::ACTION_REQUEST_LIST->value)
            ->where('ar.status = :status')
            ->setParameter('status', ActionStatus::PENDING->value)
            ->andWhere('ar.createdAt.value < :timeoutDate')
            ->setParameter('timeoutDate', $timeoutDate)
            ->orderBy('ar.createdAt', 'ASC')
        ;

        $query
            ->setFirstResult(($page - 1) * $itemsPerPage)
            ->setMaxResults($itemsPerPage)
        ;

        $paginator = new Paginator($query->getQuery());

        return new PaginatorOrm($paginator);
    }

    public function collection(array $filters = [], array $orderBy = [], int $page = 0, int $itemsPerPage = 50): PaginatorInterface
    {
        $query = $this
            ->createQueryBuilder('ar')
            ->setCacheable(true)
            ->setCacheMode(ClassMetadata::CACHE_USAGE_NONSTRICT_READ_WRITE)
            ->setCacheRegion(SystemCacheKeys::ACTION_REQUEST_LIST->value)
        ;

        if (!empty($filters)) {
            /*foreach ($filters as $field => $value) {
                switch ($field) {
                }
            }*/
        }

        if (!empty($orderBy)) {
            foreach ($orderBy as $field => $direction) {
                $query->addOrderBy('ar.'.$field, $direction);
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
