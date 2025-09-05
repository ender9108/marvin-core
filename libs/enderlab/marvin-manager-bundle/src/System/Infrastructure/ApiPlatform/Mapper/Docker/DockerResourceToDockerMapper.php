<?php
namespace EnderLab\MarvinManagerBundle\System\Infrastructure\ApiPlatform\Mapper\Docker;

use EnderLab\DddCqrsBundle\Application\Query\FindItemQuery;
use EnderLab\DddCqrsBundle\Application\Query\QueryBusInterface;
use EnderLab\DddCqrsBundle\Domain\Exception\MissingModelException;
use EnderLab\MarvinManagerBundle\System\Domain\Model\Docker;
use EnderLab\MarvinManagerBundle\System\Domain\Model\DockerCustomCommand;
use EnderLab\MarvinManagerBundle\System\Infrastructure\ApiPlatform\Resource\DockerResource;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;
use Symfonycasts\MicroMapper\MicroMapperInterface;

#[AsMapper(from: DockerResource::class, to: Docker::class)]
readonly class DockerResourceToDockerMapper implements MapperInterface
{
    public function __construct(
        private QueryBusInterface $queryBus,
        private MicroMapperInterface $microMapper,
    ) {
    }

    /**
     * @throws MissingModelException
     * @throws ExceptionInterface
     */
    public function load(object $from, string $toClass, array $context): Docker
    {
        $dto = $from;
        assert($dto instanceof DockerResource);

        $entity = $dto->id ?
            $this->queryBus->ask(new FindItemQuery($dto->id, Docker::class)) :
            new Docker()
        ;

        if (!$entity) {
            throw new MissingModelException($dto->id, Docker::class);
        }

        return $entity;
    }

    public function populate(object $from, object $to, array $context): Docker
    {
        $dto = $from;
        $entity = $to;

        assert($dto instanceof DockerResource);
        assert($entity instanceof Docker);

        $entity
            ->setContainerId($dto->containerId)
            ->setContainerName($dto->containerName)
            ->setContainerImage($dto->containerImage)
            ->setContainerStatus($dto->containerStatus)
            ->setContainerService($dto->containerService)
            ->setContainerState($dto->containerState)
            ->setContainerProject($dto->containerProject)
            ->setDefinition($dto->definition)
        ;

        foreach ($dto->dockerCustomCommands as $dockerCustomCommand) {
            $entity->addDockerCustomCommand($this->microMapper->map(
                $dockerCustomCommand,
                DockerCustomCommand::class,
                [MicroMapperInterface::MAX_DEPTH => 0]
            ));
        }

        return $entity;
    }
}
