<?php

namespace Marvin\System\Infrastructure\Persistence\Doctrine\ORM;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Marvin\System\Domain\Exception\WorkerNotFound;
use Marvin\System\Domain\Model\Worker;
use Marvin\System\Domain\Repository\WorkerRepositoryInterface;
use Marvin\System\Domain\ValueObject\Identity\WorkerId;
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
}
