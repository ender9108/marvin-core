<?php

namespace Marvin\Secret\Infrastructure\Persistence\Doctrine\ORM;

use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Marvin\Secret\Domain\Exception\SecretNotFound;
use Marvin\Secret\Domain\Model\Secret;
use Marvin\Secret\Domain\Repository\SecretRepositoryInterface;
use Marvin\Secret\Domain\ValueObject\Identity\SecretId;
use Marvin\Secret\Domain\ValueObject\SecretCategory;
use Marvin\Secret\Domain\ValueObject\SecretKey;
use Marvin\Secret\Domain\ValueObject\SecretScope;

final class SecretOrmRepository extends ServiceEntityRepository implements SecretRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Secret::class);
    }

    public function save(Secret $secret): void
    {
        $this->getEntityManager()->persist($secret);
        $this->getEntityManager()->flush();
    }

    public function remove(Secret $secret): void
    {
        $this->getEntityManager()->remove($secret);
        $this->getEntityManager()->flush();
    }

    public function byId(SecretId $id): Secret
    {
        /** @var Secret $secret */
        $secret = $this->findOneBy(['id' => $id->toString()]);

        if (null === $secret) {
            throw SecretNotFound::withId($id);
        }

        return $secret;
    }

    public function byKey(SecretKey $key): ?Secret
    {
        return $this->createQueryBuilder('s')
            ->where('s.key.value = :key')
            ->setParameter('key', $key->value)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function byScope(SecretScope $scope): array
    {
        return $this->findBy(['scope' => $scope]);
    }

    public function byCategory(SecretCategory $category): array
    {
        return $this->findBy(['category' => $category]);
    }

    public function exists(SecretKey $key): bool
    {
        return $this->byKey($key) !== null;
    }

    public function all(): array
    {
        return $this->findAll();
    }

    public function getNeedingRotation(): array
    {
        $query = $this->createQueryBuilder('s');

        return $query
            ->join('s.rotationPolicy', 'rp')
            ->where('rp.autoRotate = :autoRotate')
            ->andWhere('s.lastRotatedAt IS NOT NULL')
            ->andWhere(
                $query->expr()->lte(
                    'DATE_ADD(s.lastRotatedAt, rp.rotationIntervalDays, \'DAY\')',
                    ':now'
                )
            )
            ->setParameter('autoRotate', true)
            ->setParameter('now', new DateTimeImmutable())
            ->getQuery()
            ->getResult()
        ;
    }

    public function getExpired(): array
    {
        return $this
            ->createQueryBuilder('s')
            ->where('s.expiresAt IS NOT NULL')
            ->andWhere('s.expiresAt <= :now')
            ->setParameter('now', new \DateTimeImmutable())
            ->getQuery()
            ->getResult()
        ;
    }
}
