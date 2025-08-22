<?php

namespace App\System\Infrastructure\Doctrine\Repository;

use App\System\Domain\Model\UserStatus;
use App\System\Domain\Repository\UserStatusRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class DoctrineUserStatusRepository extends ServiceEntityRepository implements UserStatusRepositoryInterface
{
    private const string ENTITY_CLASS = UserStatus::class;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, self::ENTITY_CLASS);
    }

    public function add(UserStatus $userStatus): void
    {
        $this->getEntityManager()->persist($userStatus);
    }

    public function remove(UserStatus $userStatus): void
    {
        $this->getEntityManager()->remove($userStatus);
    }

    public function getById(int $id): ?UserStatus
    {
        return $this->find($id);
    }

    public function getByReference(string $reference): ?UserStatus
    {
        return $this
            ->createQueryBuilder('us')
            ->andWhere('us.reference = :reference')
            ->setParameter('reference', $reference)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
