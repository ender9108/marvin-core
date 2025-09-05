<?php
namespace Marvin\Security\Infrastructure\Persistence\Doctrine\ORM;

use Doctrine\ORM\Query\Expr\Join;
use Marvin\Security\Domain\Exception\UserNotFound;
use Marvin\Security\Domain\Model\User;
use Marvin\Security\Domain\Model\UserStatus;
use Marvin\Security\Domain\Repository\UserRepositoryInterface;
use Marvin\Security\Domain\ValueObject\Identity\UserId;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Marvin\Shared\Domain\ValueObject\Email;
use Override;

/**
 * @extends ServiceEntityRepository<User>
 */
final class UserOrmRepository extends ServiceEntityRepository implements UserRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    #[Override]
    public function save(User $user, bool $flush = true): void
    {
        $this->getEntityManager()->persist($user);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    #[Override]
    public function remove(User $user, bool $flush = true): void
    {
        $this->getEntityManager()->remove($user);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function countSameEnabledUserType(User $user): int
    {
        return $this
            ->createQueryBuilder('u')
            ->select('COUNT(u)')
            ->innerJoin('u.status', 's', Join::WITH, 's = u.status')
            ->innerJoin('u.type', 't', Join::WITH, 't = u.type')
            ->where('u.id != :id')
            ->setParameter('id', $user->id)
            ->andWhere('s.reference.reference = :reference')
            ->setParameter('reference', UserStatus::STATUS_ENABLED)
            ->andWhere('t = :type')
            ->setParameter('type', $user->type)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    #[Override]
    public function byId(UserId $userId): User
    {
        $user = $this->findOneBy(['id' => $userId->toString()]);

        if ($user === null) {
            throw UserNotFound::withId($userId);
        }

        return $user;
    }

    #[Override]
    public function byEmail(Email $email): User
    {
        $user = $this->findOneBy(['email' => $email->email]);

        if ($user === null) {
            throw UserNotFound::withEmail($email);
        }

        return $user;
    }

    #[Override]
    public function byIdentifier(string $identifier): User
    {
        return $this->byEmail(new Email($identifier));
    }
}
