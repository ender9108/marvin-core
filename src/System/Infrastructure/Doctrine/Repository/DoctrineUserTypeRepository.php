<?php

namespace App\System\Infrastructure\Doctrine\Repository;

use App\System\Domain\Model\UserType;
use App\System\Domain\Repository\UserTypeRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class DoctrineUserTypeRepository extends ServiceEntityRepository implements UserTypeRepositoryInterface
{
    private const string ENTITY_CLASS = UserType::class;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, self::ENTITY_CLASS);
    }

    public function add(UserType $userType): void
    {
        $this->getEntityManager()->persist($userType);
    }

    public function remove(UserType $userType): void
    {
        $this->getEntityManager()->remove($userType);
    }

    public function getById(string $id): ?UserType
    {
        return $this->find($id);
    }

    public function getByReference(string $reference): ?UserType
    {
        return $this
            ->createQueryBuilder('ut')
            ->andWhere('ut.reference = :reference')
            ->setParameter('reference', $reference)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
