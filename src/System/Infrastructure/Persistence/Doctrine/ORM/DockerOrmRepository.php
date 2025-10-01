<?php

namespace Marvin\System\Infrastructure\Persistence\Doctrine\ORM;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Marvin\System\Domain\Model\Docker;
use Marvin\System\Domain\Repository\DockerRepositoryInterface;
use Marvin\System\Domain\ValueObject\Identity\DockerId;

/**
 * @extends ServiceEntityRepository<Docker>
 */
final class DockerOrmRepository extends ServiceEntityRepository implements DockerRepositoryInterface
{
    public const string MODEL_CLASS = Docker::class;
    public const string MODEL_ALIAS = 'docker';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, self::MODEL_CLASS);
    }

    #[\Override]
    public function save(Docker $docker, bool $flush = true): void
    {
        $this->getEntityManager()->persist($docker);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    #[\Override]
    public function remove(Docker $docker, bool $flush = true): void
    {
        $this->getEntityManager()->remove($docker);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    #[\Override]
    public function byId(DockerId $dockerId): Docker
    {
        $docker = $this->findOneBy(['id' => $dockerId->toString()]);

        if ($docker === null) {
            throw DockerNotFound::withId($dockerId);
        }

        return $docker;
    }
}
