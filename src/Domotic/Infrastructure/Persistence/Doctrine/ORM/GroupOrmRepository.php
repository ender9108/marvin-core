<?php

namespace Marvin\Domotic\Infrastructure\Persistence\Doctrine\ORM;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Marvin\Domotic\Domain\Exception\GroupNotFound;
use Marvin\Domotic\Domain\Model\Group;
use Marvin\Domotic\Domain\Repository\GroupRepositoryInterface;
use Marvin\Domotic\Domain\ValueObject\Identity\GroupId;
use Override;

/**
 * @extends ServiceEntityRepository<Group>
 */
final class GroupOrmRepository extends ServiceEntityRepository implements GroupRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Group::class);
    }

    #[Override]
    public function save(Group $model, bool $flush = true): void
    {
        $this->getEntityManager()->persist($model);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    #[Override]
    public function remove(Group $model, bool $flush = true): void
    {
        $this->getEntityManager()->remove($model);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    #[Override]
    public function byId(GroupId $id): Group
    {
        $entity = $this->findOneBy(['id' => $id]);
        if (null === $entity) {
            throw GroupNotFound::withId($id);
        }
        return $entity;
    }
}
