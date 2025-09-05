<?php
namespace Marvin\Security\Infrastructure\Persistence\Doctrine\ORM;

use Marvin\Security\Domain\Exception\UserTypeNotFound;
use Marvin\Security\Domain\Model\UserType;
use Marvin\Security\Domain\Repository\UserTypeRepositoryInterface;
use Marvin\Security\Domain\ValueObject\Identity\UserTypeId;
use Marvin\Shared\Domain\ValueObject\Reference;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Override;

/**
 * @extends ServiceEntityRepository<UserType>
 */
final class UserTypeOrmRepository extends ServiceEntityRepository implements UserTypeRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserType::class);
    }

    #[Override]
    public function save(UserType $userType, bool $flush = true): void
    {
        $this->getEntityManager()->persist($userType);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    #[Override]
    public function remove(UserType $userType, bool $flush = true): void
    {
        $this->getEntityManager()->remove($userType);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    #[Override]
    public function byId(UserTypeId $id): UserType
    {
        $userType = $this->findOneBy(['id' => $id]);

        if ($userType === null) {
            throw UserTypeNotFound::withId($id);
        }

        return $userType;
    }

    #[Override]
    public function byReference(Reference $reference): UserType
    {
        $userType = $this->findOneBy(['reference.reference' => $reference->reference]);

        if (null === $userType) {
            throw UserTypeNotFound::withReference($reference);
        }

        return $userType;
    }
}
