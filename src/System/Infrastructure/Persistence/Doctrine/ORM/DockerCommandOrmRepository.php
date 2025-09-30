<?php
namespace Marvin\System\Infrastructure\Persistence\Doctrine\ORM;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Marvin\System\Domain\Model\Docker;
use Marvin\System\Domain\Model\DockerCommand;
use Marvin\System\Domain\Repository\DockerCommandRepositoryInterface;
use Marvin\System\Domain\Repository\DockerRepositoryInterface;
use Marvin\System\Domain\ValueObject\Identity\DockerCommandId;
use Marvin\System\Domain\ValueObject\Identity\DockerId;

/**
 * @extends ServiceEntityRepository<DockerCommand>
 */
final class DockerCommandOrmRepository extends ServiceEntityRepository implements DockerCommandRepositoryInterface
{
    public const string MODEL_CLASS = DockerCommand::class;
    public const string MODEL_ALIAS = 'docker_command';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, self::MODEL_CLASS);
    }

    #[\Override]
    public function save(DockerCommand $dockerCommand, bool $flush = true): void
    {
        $this->getEntityManager()->persist($dockerCommand);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    #[\Override]
    public function remove(DockerCommand $dockerCommand, bool $flush = true): void
    {
        $this->getEntityManager()->remove($dockerCommand);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    #[\Override]
    public function byId(DockerCommandId $dockerCommandId): DockerCommand
    {
        $dockerCommand = $this->findOneBy(['id' => $dockerCommandId->toString()]);

        if ($dockerCommand === null) {
            throw DockerCommandNotFound::withId($dockerCommandId);
        }

        return $dockerCommand;
    }
}
