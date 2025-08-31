<?php
namespace EnderLab\MarvinManagerBundle\System\Infrastructure\ApiPlatform\Mapper\Docker;

use EnderLab\MarvinManagerBundle\System\Domain\Model\Docker;
use EnderLab\MarvinManagerBundle\System\Infrastructure\ApiPlatform\Resource\DockerCustomCommandResource;
use EnderLab\MarvinManagerBundle\System\Infrastructure\ApiPlatform\Resource\DockerResource;
use Psr\Cache\InvalidArgumentException;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;
use Symfonycasts\MicroMapper\MicroMapperInterface;

#[AsMapper(from: Docker::class, to: DockerResource::class)]
readonly class DockerToDockerResourceMapper implements MapperInterface
{
    public function __construct(
        private MicroMapperInterface $microMapper,
    ) {
    }

    public function load(object $from, string $toClass, array $context): DockerResource
    {
        $entity = $from;
        assert($entity instanceof Docker);
        $dto = new DockerResource();
        $dto->id = $entity->getId();

        return $dto;
    }

    public function populate(object $from, object $to, array $context): DockerResource
    {
        $entity = $from;
        $dto = $to;

        assert($entity instanceof Docker);
        assert($dto instanceof DockerResource);

        $dto->containerId = $entity->getContainerId();
        $dto->containerName = $entity->getContainerName();
        $dto->containerImage = $entity->getContainerImage();
        $dto->containerStatus = $entity->getContainerStatus();
        $dto->containerService = $entity->getContainerService();
        $dto->containerState = $entity->getContainerState();
        $dto->containerProject = $entity->getContainerProject();
        $dto->definition = $entity->getDefinition();
        $dto->dockerCustomCommands = $this->microMapper->mapMultiple(
            $entity->getDockerCustomCommands(),
            DockerCustomCommandResource::class,
            [MicroMapperInterface::MAX_DEPTH => 0]
        );
        $dto->createdAt = $entity->getCreatedAt();
        $dto->updatedAt = $entity->getUpdatedAt();
        $dto->createdBy = $entity->getCreatedBy();
        $dto->updatedBy = $entity->getUpdatedBy();

        return $dto;
    }
}
