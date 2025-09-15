<?php
namespace Marvin\Security\Infrastructure\Persistence\Doctrine\ORM;

use Marvin\Security\Domain\Exception\UserStatusNotFound;
use Marvin\Security\Domain\Repository\UserStatusRepositoryInterface;
use Marvin\Security\Domain\ValueObject\Identity\UserStatusId;
use Marvin\Shared\Domain\ValueObject\Reference;
use Marvin\Security\Domain\Model\UserStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Override;

/**
 * @extends ServiceEntityRepository<UserStatus>
 */
final class UserStatusOrmRepository extends ServiceEntityRepository implements UserStatusRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserStatus::class);
    }

    #[Override]
    public function save(UserStatus $userStatus, bool $flush = true): void
    {
        $this->getEntityManager()->persist($userStatus);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    #[Override]
    public function remove(UserStatus $userStatus, bool $flush = true): void
    {
        $this->getEntityManager()->remove($userStatus);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    #[Override]
    public function byId(UserStatusId $id): UserStatus
    {
        $userStatus = $this->findOneBy(['id' => $id]);

        if ($userStatus === null) {
            throw UserStatusNotFound::withId($id);
        }

        return $userStatus;
    }

    #[Override]
    public function byReference(Reference $reference): UserStatus
    {
        $userStatus = $this->findOneBy(['reference.value' => $reference->value]);

        if ($userStatus === null) {
            throw UserStatusNotFound::withReference($reference);
        }

        return $userStatus;
    }
}
