<?php

namespace Marvin\Security\Infrastructure\Persistence\Doctrine\ORM;

use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Marvin\Security\Domain\Exception\RequestResetPasswordNotFound;
use Marvin\Security\Domain\Model\RequestResetPassword;
use Marvin\Security\Domain\Model\User;
use Marvin\Security\Domain\Repository\RequestResetPasswordRepositoryInterface;
use Marvin\Security\Domain\ValueObject\Identity\RequestResetPasswordId;

/**
 * @extends ServiceEntityRepository<RequestResetPassword>
 */
final class RequestResetPasswordOrmRepository extends ServiceEntityRepository implements RequestResetPasswordRepositoryInterface
{
    public const string MODEL_CLASS = RequestResetPassword::class;
    public const string MODEL_ALIAS = 'request_reset_password';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, self::MODEL_CLASS);
    }

    public function save(RequestResetPassword $request, bool $flush = true): void
    {
        $this->getEntityManager()->persist($request);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(RequestResetPassword $request, bool $flush = true): void
    {
        $this->getEntityManager()->remove($request);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function byId(RequestResetPasswordId $id): RequestResetPassword
    {
        $entity = $this->findOneBy(['id' => $id->toString()]);

        if ($entity === null) {
            throw RequestResetPasswordNotFound::withId($id);
        }

        return $entity;
    }

    public function byToken(string $token): RequestResetPassword
    {
        $entity = $this->findOneBy(['token' => $token]);

        if ($entity === null) {
            throw RequestResetPasswordNotFound::withToken($token);
        }

        return $entity;
    }

    public function checkIfRequestAlreadyExists(User $user): bool
    {
        $count = $this->createQueryBuilder(self::MODEL_ALIAS)
            ->select('COUNT('.self::MODEL_ALIAS.')')
            ->where(self::MODEL_ALIAS.'.user = :user')
            ->setParameter('user', $user)
            ->andWhere(self::MODEL_ALIAS.'.expiresAt.value > :now')
            ->setParameter('now', new DateTimeImmutable())
            ->andWhere(self::MODEL_ALIAS.'.used = false')
            ->getQuery()
            ->getSingleScalarResult()
        ;

        return $count > 0;
    }
}
