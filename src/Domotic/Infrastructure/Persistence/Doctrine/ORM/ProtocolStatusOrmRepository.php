<?php

namespace Marvin\Domotic\Infrastructure\Persistence\Doctrine\ORM;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Marvin\Domotic\Domain\Exception\ProtocolStatusNotFound;
use Marvin\Domotic\Domain\Model\ProtocolStatus;
use Marvin\Domotic\Domain\Repository\ProtocolStatusRepositoryInterface;
use Marvin\Domotic\Domain\ValueObject\Identity\ProtocolStatusId;
use Override;

/**
 * @extends ServiceEntityRepository<ProtocolStatus>
 */
final class ProtocolStatusOrmRepository extends ServiceEntityRepository implements ProtocolStatusRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProtocolStatus::class);
    }

    #[Override]
    public function save(ProtocolStatus $model, bool $flush = true): void
    {
        $this->getEntityManager()->persist($model);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    #[Override]
    public function remove(ProtocolStatus $model, bool $flush = true): void
    {
        $this->getEntityManager()->remove($model);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    #[Override]
    public function byId(ProtocolStatusId $id): ProtocolStatus
    {
        $entity = $this->findOneBy(['id' => $id]);
        if (null === $entity) {
            throw ProtocolStatusNotFound::withId($id);
        }
        return $entity;
    }
}
