<?php
namespace EnderLab\MarvinManagerBundle\System\Domain\Repository;

use EnderLab\DddCqrsBundle\Domain\Repository\RepositoryInterface;
use EnderLab\MarvinManagerBundle\System\Domain\Model\DockerCustomCommand;

interface DockerCustomCommandRepositoryInterface extends RepositoryInterface
{
    public function add(DockerCustomCommand $dockerCustomCommand): void;

    public function remove(DockerCustomCommand $dockerCustomCommand): void;

    public function byId(string $id): ?DockerCustomCommand;
}
