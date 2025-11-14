<?php
/**
 * Marvin Core - DDD-based home automation system
 *
 * @package   Marvin\Core
 * @author    Alexandre Berthelot <alexandreberthelot9108@gmail.com>
 * @copyright 2024-present Alexandre Berthelot
 * @license   AGPL-3.0 License
 * @link      https://github.com/ender9108/marvin-core
 */

declare(strict_types=1);

namespace Marvin\Secret\Infrastructure\Persistence\Doctrine\ORM;

use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use EnderLab\DddCqrsBundle\Domain\Repository\PaginatorInterface;
use EnderLab\DddCqrsBundle\Infrastructure\Persistence\Doctrine\ORM\PaginatorOrm;
use Marvin\Secret\Domain\Exception\SecretNotFound;
use Marvin\Secret\Domain\Model\Secret;
use Marvin\Secret\Domain\Repository\SecretRepositoryInterface;
use Marvin\Secret\Domain\ValueObject\Identity\SecretId;
use Marvin\Secret\Domain\ValueObject\SecretCategory;
use Marvin\Secret\Domain\ValueObject\SecretKey;
use Marvin\Secret\Domain\ValueObject\SecretScope;
use Marvin\Secret\Infrastructure\Persistence\Doctrine\Cache\SecretCacheKeys;

final class SecretOrmRepository extends ServiceEntityRepository implements SecretRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Secret::class);
    }

    public function save(Secret $secret, bool $flush = true): void
    {
        $this->getEntityManager()->persist($secret);

        if (true === $flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Secret $secret, bool $flush = true): void
    {
        $this->getEntityManager()->remove($secret);

        if (true === $flush) {
            $this->getEntityManager()->flush();
        }
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
            ->where('s.rotationPolicy.autoRotate = :autoRotate')
            ->setParameter('autoRotate', true)
            ->andWhere('s.lastRotatedAt IS NOT NULL')
            ->andWhere(
                $query->expr()->lte(
                    'DATE_ADD(s.lastRotatedAt, s.rotationPolicy.rotationIntervalDays, \'DAY\')',
                    ':now'
                )
            )
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
            ->setParameter('now', new DateTimeImmutable())
            ->getQuery()
            ->getResult()
        ;
    }

    public function collection(
        /** @var array<string, mixed> $criterias */
        array $criterias = [],
        /** @var array<string, string> $orderBy */
        array $orderBy = [],
        int $page = 0,
        int $itemsPerPage = 50
    ): PaginatorInterface {
        $query = $this
            ->createQueryBuilder('s')
            ->setCacheable(true)
            ->setCacheMode(ClassMetadata::CACHE_USAGE_NONSTRICT_READ_WRITE)
            ->setCacheRegion(SecretCacheKeys::SECRET_LIST->value)
        ;

        foreach ($criterias as $field => $value) {
            switch ($field) {
                case 'key':
                    $query
                        ->andWhere('s.key.value = :key')
                        ->setParameter('key', $value)
                    ;
                    break;
                case 'scope':
                    $query
                        ->andWhere('s.scope = :scope')
                        ->setParameter('scope', $value)
                    ;
                    break;
                case 'category':
                    $query
                        ->andWhere('s.category = :category')
                        ->setParameter('category', $value)
                    ;
                    break;
                case 'managed':
                    $query
                        ->andWhere('s.rotationPolicy.autoRotate = :managed')
                        ->setParameter('managed', $value)
                    ;
                    break;
            }
        }

        foreach ($orderBy as $field => $direction) {
            $query->addOrderBy('s.' . $field, $direction);
        }

        $query->setFirstResult(($page - 1) * $itemsPerPage);
        $query->setMaxResults($itemsPerPage);

        $paginator = new Paginator($query->getQuery());

        return new PaginatorOrm($paginator);
    }
}
