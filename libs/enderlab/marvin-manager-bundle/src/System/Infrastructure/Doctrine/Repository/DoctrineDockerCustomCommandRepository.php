<?php
namespace EnderLab\MarvinManagerBundle\System\Infrastructure\Doctrine\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use EnderLab\MarvinManagerBundle\System\Domain\Model\DockerCustomCommand;
use EnderLab\MarvinManagerBundle\System\Domain\Repository\DockerCustomCommandRepositoryInterface;

final class DoctrineDockerCustomCommandRepository extends ServiceEntityRepository implements DockerCustomCommandRepositoryInterface
{
    private const string ENTITY_CLASS = DockerCustomCommand::class;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, self::ENTITY_CLASS);
    }

    public function add(DockerCustomCommand $dockerCustomCommand): void
    {
        $this->getEntityManager()->persist($dockerCustomCommand);
    }

    public function remove(DockerCustomCommand $dockerCustomCommand): void
    {
        $this->getEntityManager()->remove($dockerCustomCommand);
    }

    public function byId(string $id): ?DockerCustomCommand
    {
        return $this->find($id);
    }
}
