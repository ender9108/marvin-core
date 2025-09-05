<?php
namespace EnderLab\MarvinManagerBundle\System\Infrastructure\Persistence\Doctrine\ORM;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use EnderLab\MarvinManagerBundle\System\Domain\Exception\DockerCustomCommandNotFound;
use EnderLab\MarvinManagerBundle\System\Domain\Model\DockerCustomCommand;
use EnderLab\MarvinManagerBundle\System\Domain\Repository\DockerCustomCommandRepositoryInterface;
use EnderLab\MarvinManagerBundle\System\Domain\ValueObject\Identity\DockerCustomCommandId;
use Override;

/**
 * @extends ServiceEntityRepository<DockerCustomCommand>
 */
final class DockerOrmRepository extends ServiceEntityRepository implements DockerCustomCommandRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DockerCustomCommand::class);
    }

    #[Override]
    public function add(DockerCustomCommand $command): void
    {
        $this->getEntityManager()->persist($command);
    }

    #[Override]
    public function remove(DockerCustomCommand $command): void
    {
        $this->getEntityManager()->remove($command);
    }

    #[Override]
    public function byId(DockerCustomCommandId $id): DockerCustomCommand
    {
        $docker = $this->findOneBy(['id' => $id]);

        if ($docker === null) {
            throw DockerCustomCommandNotFound::withId($id);
        }

        return $docker;
    }
}
