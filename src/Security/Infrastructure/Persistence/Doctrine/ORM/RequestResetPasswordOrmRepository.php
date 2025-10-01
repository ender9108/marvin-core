<?php

namespace Marvin\Security\Infrastructure\Persistence\Doctrine\ORM;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Marvin\Security\Domain\Model\RequestResetPassword;
use Marvin\Security\Domain\Repository\RequestResetPasswordRepositoryInterface;
use Marvin\Security\Domain\ValueObject\Identity\RequestResetPasswordIdType;

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

    public function byId(RequestResetPasswordIdType $id): RequestResetPassword
    {
        $entity = $this->findOneBy(['id' => $id->toString()]);

        if ($entity === null) {
            throw new \RuntimeException(sprintf('RequestResetPassword with id %s not found', $id->toString()));
        }

        return $entity;
    }

    public function byToken(string $token): RequestResetPassword
    {
        $entity = $this->findOneBy(['token' => $token]);

        if ($entity === null) {
            throw new \RuntimeException(sprintf('RequestResetPassword with id %s not found', $token));
        }

        return $entity;
    }
}
