<?php
namespace EnderLab\MarvinManagerBundle\System\Infrastructure\Doctrine\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use EnderLab\MarvinManagerBundle\System\Domain\Model\Docker;
use EnderLab\MarvinManagerBundle\System\Domain\Repository\DockerRepositoryInterface;

final class DoctrineDockerRepository extends ServiceEntityRepository implements DockerRepositoryInterface
{
    private const string ENTITY_CLASS = Docker::class;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, self::ENTITY_CLASS);
    }

    public function add(Docker $docker): void
    {
        $this->getEntityManager()->persist($docker);
    }

    public function remove(Docker $docker): void
    {
        $this->getEntityManager()->remove($docker);
    }

    public function byId(string $id): ?Docker
    {
        return $this->find($id);
    }
}
