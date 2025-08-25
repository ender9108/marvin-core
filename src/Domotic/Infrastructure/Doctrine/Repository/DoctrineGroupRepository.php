<?php

namespace App\Domotic\Infrastructure\Doctrine\Repository;

use App\Domotic\Domain\Model\Group;
use App\Domotic\Domain\Repository\GroupRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class DoctrineGroupRepository extends ServiceEntityRepository implements GroupRepositoryInterface
{
    private const string ENTITY_CLASS = Group::class;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, self::ENTITY_CLASS);
    }

    public function add(Group $group): void
    {
        $this->getEntityManager()->persist($group);
    }

    public function remove(Group $group): void
    {
        $this->getEntityManager()->remove($group);
    }

    public function byId(string $id): ?Group
    {
        return $this->find($id);
    }
}
