<?php

namespace Marvin\Security\Infrastructure\Persistence\Doctrine\ORM;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use EnderLab\DddCqrsBundle\Domain\Repository\PaginatorInterface;
use EnderLab\DddCqrsBundle\Infrastructure\Persistence\Doctrine\ORM\PaginatorOrm;
use Marvin\Security\Domain\Exception\UserNotFound;
use Marvin\Security\Domain\Model\User;
use Marvin\Security\Domain\Model\UserStatus;
use Marvin\Security\Domain\Repository\UserRepositoryInterface;
use Marvin\Security\Domain\ValueObject\Identity\UserId;
use Marvin\Security\Infrastructure\Persistence\Doctrine\Cache\UserCacheKeys;
use Marvin\Shared\Domain\ValueObject\Email;
use Marvin\Shared\Domain\ValueObject\Reference;
use Override;

/**
 * @extends ServiceEntityRepository<User>
 */
final class UserOrmRepository extends ServiceEntityRepository implements UserRepositoryInterface
{
    public const string MODEL_CLASS = User::class;
    public const string MODEL_ALIAS = 'user';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, self::MODEL_CLASS);
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
            ->andWhere('s.reference = :reference')
            ->setParameter('reference', new Reference(UserStatus::STATUS_ENABLED))
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
    public function byEmail(Email $email): ?User
    {
        return $this->findOneBy(['email' => (string) $email]);
    }

    #[Override]
    public function byIdentifier(string $identifier): ?User
    {
        return $this->byEmail(new Email($identifier));
    }

    public function getUserCollection(
        /** @var array<string, mixed> $criterias */
        array $criterias = [],
        /** @var array<string, string> $orderBy */
        array $orderBy = [],
        int $page = 0,
        int $itemsPerPage = 20
    ): PaginatorInterface {
        $query = $this
            ->createQueryBuilder('u')
            ->setCacheable(true)
            ->setCacheMode(ClassMetadata::CACHE_USAGE_NONSTRICT_READ_WRITE)
            ->setCacheRegion(UserCacheKeys::USER_LIST->value)
        ;

        foreach ($criterias as $field => $value) {
            // @todo
        }

        foreach ($orderBy as $field => $direction) {
            $query->addOrderBy('u.' . $field, $direction);
        }

        $query->setFirstResult(($page - 1) * $itemsPerPage);
        $query->setMaxResults($itemsPerPage);

        $paginator = new Paginator($query->getQuery());

        return new PaginatorOrm($paginator);
    }
}
