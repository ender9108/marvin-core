<?php
namespace EnderLab\MarvinManagerBundle\System\Infrastructure\Persistence\Doctrine\ORM;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use EnderLab\MarvinManagerBundle\System\Domain\Exception\DockerNotFound;
use EnderLab\MarvinManagerBundle\System\Domain\Model\Docker;
use EnderLab\MarvinManagerBundle\System\Domain\Repository\DockerRepositoryInterface;
use EnderLab\MarvinManagerBundle\System\Domain\ValueObject\Identity\DockerId;
use Override;

/**
 * @extends ServiceEntityRepository<Docker>
 */
final class DockerOrmRepository extends ServiceEntityRepository implements DockerRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Docker::class);
    }

    #[Override]
    public function add(Docker $docker): void
    {
        $this->getEntityManager()->persist($docker);
    }

    #[Override]
    public function remove(Docker $docker): void
    {
        $this->getEntityManager()->remove($docker);
    }

    #[Override]
    public function byId(DockerId $id): Docker
    {
        $docker = $this->findOneBy(['id' => $id]);

        if ($docker === null) {
            throw DockerNotFound::withId($id);
        }

        return $docker;
    }
}
