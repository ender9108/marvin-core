<?php

namespace Marvin\Domotic\Infrastructure\Persistence\Doctrine\ORM;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Marvin\Domotic\Domain\Exception\ProtocolNotFound;
use Marvin\Domotic\Domain\Model\Protocol;
use Marvin\Domotic\Domain\Repository\ProtocolRepositoryInterface;
use Marvin\Domotic\Domain\ValueObject\Identity\ProtocolId;
use Override;

/**
 * @extends ServiceEntityRepository<Protocol>
 */
final class ProtocolOrmRepository extends ServiceEntityRepository implements ProtocolRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Protocol::class);
    }

    #[Override]
    public function save(Protocol $model, bool $flush = true): void
    {
        $this->getEntityManager()->persist($model);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    #[Override]
    public function remove(Protocol $model, bool $flush = true): void
    {
        $this->getEntityManager()->remove($model);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    #[Override]
    public function byId(ProtocolId $id): Protocol
    {
        $entity = $this->findOneBy(['id' => $id]);
        if (null === $entity) {
            throw ProtocolNotFound::withId($id);
        }
        return $entity;
    }
}
